<?php

declare(strict_types=1);

namespace Xitara\ERecht24\Classes;

use eRecht24\RechtstexteSDK\ApiHandler;
use Input;
use InvalidArgumentException;
use Throwable;
use Xitara\ERecht24\Models\Settings;
use Xitara\ERecht24\Models\Text;

class ApiClient
{
    public const CREDENTIAL_MODE_CONFIGURED = 'configured';
    public const CREDENTIAL_MODE_DEMO = 'demo';
    public const TEST_API_KEY = 'e81cbf18a5239377aa4972773d34cc2b81ebc672879581bce29a0a4c414bf117';
    public const TEST_PLUGIN_KEY = '3jh4uhn8u69i97kj9timk466748996ikhkjhlk67plli08lhkijgh8z4363gr53v';

    private const PLUGIN_KEY = 'bvC59XwtUueJHtFyHu7EenvQNdFCq2yM9ZnxDuHJrCgxx2HmedKVJE3ckfvvhJX2';
    private const DOCUMENT_TYPES = [
        'imprint',
        'privacyPolicy',
        'privacyPolicySocialMedia',
    ];
    private const LANGUAGES = ['de', 'en'];

    /** @var ApiHandler|null */
    private $apiHandler;

    /** @var string */
    private $credentialMode = self::CREDENTIAL_MODE_CONFIGURED;

    public function __construct(?string $apiKey = null, ?string $pluginKey = null)
    {
        if ($apiKey === null) {
            if (self::usesDemoCredentials()) {
                $apiKey = self::TEST_API_KEY;
                $pluginKey = self::TEST_PLUGIN_KEY;
                $this->credentialMode = self::CREDENTIAL_MODE_DEMO;
            } else {
                $apiKey = (string) Settings::get('apikey', '');
                $pluginKey = self::PLUGIN_KEY;
            }
        } elseif ($apiKey === self::TEST_API_KEY && $pluginKey === self::TEST_PLUGIN_KEY) {
            $this->credentialMode = self::CREDENTIAL_MODE_DEMO;
        }

        if ($apiKey !== '') {
            $this->apiHandler = new ApiHandler($apiKey, $pluginKey ?: self::PLUGIN_KEY);
        }
    }

    public static function forCredentialMode(string $mode) : self
    {
        if (self::normalizeCredentialMode($mode) === self::CREDENTIAL_MODE_DEMO) {
            return new self(self::TEST_API_KEY, self::TEST_PLUGIN_KEY);
        }

        return new self((string) Settings::get('apikey', ''), self::PLUGIN_KEY);
    }

    public static function getCredentialMode() : string
    {
        return self::usesDemoCredentials()
            ? self::CREDENTIAL_MODE_DEMO
            : self::CREDENTIAL_MODE_CONFIGURED;
    }

    public static function getSubmittedCredentialMode() : string
    {
        $postedSettings = Input::get('Settings', []);

        if (is_array($postedSettings) && array_key_exists('use_demo_credentials', $postedSettings)) {
            return (bool) $postedSettings['use_demo_credentials']
                ? self::CREDENTIAL_MODE_DEMO
                : self::CREDENTIAL_MODE_CONFIGURED;
        }

        return self::getCredentialMode();
    }

    public static function normalizeCredentialMode(string $mode) : string
    {
        return $mode === self::CREDENTIAL_MODE_DEMO
            ? self::CREDENTIAL_MODE_DEMO
            : self::CREDENTIAL_MODE_CONFIGURED;
    }

    public static function usesDemoCredentials() : bool
    {
        return (bool) Settings::get('use_demo_credentials', false);
    }

    public function getHandler() : ApiHandler
    {
        if (!$this->apiHandler instanceof ApiHandler) {
            throw new InvalidArgumentException('No eRecht24 API key configured.');
        }

        return $this->apiHandler;
    }

    public function testApiKey() : array
    {
        $postedSettings = Input::get('Settings', []);
        $credentialMode = self::getSubmittedCredentialMode();
        $apiKey = is_array($postedSettings)
            ? ($postedSettings['apikey'] ?? null)
            : Input::get('Settings.apikey');

        if ($credentialMode === self::CREDENTIAL_MODE_DEMO) {
            $this->apiHandler = new ApiHandler(self::TEST_API_KEY, self::TEST_PLUGIN_KEY);
            $this->credentialMode = self::CREDENTIAL_MODE_DEMO;

            return $this->importDocument('imprint', ['de'], 'api_key_test');
        }

        if (!is_string($apiKey) || trim($apiKey) === '') {
            return [
                'success' => false,
                'code' => null,
                'body' => null,
                'error' => 'no_api_key_found_in_post_data',
            ];
        }

        $this->apiHandler = new ApiHandler(trim($apiKey), self::PLUGIN_KEY);
        $this->credentialMode = self::CREDENTIAL_MODE_CONFIGURED;

        return $this->importDocument('imprint', ['de'], 'api_key_test');
    }

    public static function imprint($lang = 'de') : array
    {
        return (new self())->importDocument('imprint', [$lang], 'pull');
    }

    public static function privacyPolicy($lang = 'de') : array
    {
        return (new self())->importDocument('privacyPolicy', [$lang], 'pull');
    }

    public static function privacyPolicySocialMedia($lang = 'de') : array
    {
        return (new self())->importDocument('privacyPolicySocialMedia', [$lang], 'pull');
    }

    /**
     * Fetches a document once and stores all requested language variants.
     */
    public function importDocument(
        string $type,
        array $languages,
        string $source = 'pull',
        bool $persist = true
    ) : array {
        if (!in_array($type, self::DOCUMENT_TYPES, true)) {
            throw new InvalidArgumentException('Invalid eRecht24 document type.');
        }

        $languages = array_values(array_intersect(self::LANGUAGES, array_unique($languages)));
        if ($languages === []) {
            throw new InvalidArgumentException('No supported language selected.');
        }

        $handler = $this->getHandler();
        $logContext = [
            'document_type' => $type,
            'languages' => $languages,
            'source' => $source,
            'persist' => $persist,
        ];
        ApiRequestLogger::started('document.fetch', $this->credentialMode, $logContext);

        try {
            $document = $this->fetchDocument($handler, $type);
        } catch (Throwable $exception) {
            ApiRequestLogger::exception('document.fetch', $this->credentialMode, $exception, $logContext);
            throw $exception;
        }

        $response = $this->getLastResponse();

        if (!$handler->isLastResponseSuccess()) {
            $result = [
                'success' => false,
                'code' => $response['code'],
                'body' => $response['body'],
                'error' => 'error_getting_' . $this->errorType($type),
            ];
            ApiRequestLogger::failed(
                'document.fetch',
                $this->credentialMode,
                $response['code'],
                $result['error'],
                $logContext
            );

            if ($persist) {
                StatusRepository::markDocumentError($type, $languages, $result['error'], $source);
            }

            return $result;
        }

        $changed = [];
        $missingLanguages = [];

        foreach ($languages as $lang) {
            $html = $document->getHtml($lang);
            if (!is_string($html) || $html === '') {
                $missingLanguages[] = $lang;
                continue;
            }

            if ($persist) {
                $changed[$lang] = $this->updateText($type, $lang, $html);
            } else {
                $changed[$lang] = false;
            }
        }

        if ($missingLanguages !== []) {
            $error = 'missing_document_language';
            ApiRequestLogger::failed(
                'document.fetch',
                $this->credentialMode,
                $response['code'],
                $error,
                array_merge($logContext, ['missing_languages' => $missingLanguages])
            );
            if ($persist) {
                StatusRepository::markDocumentError($type, $missingLanguages, $error, $source);
            }

            return [
                'success' => false,
                'code' => $response['code'],
                'body' => $response['body'],
                'error' => $error,
            ];
        }

        $warning = $document->getWarnings();
        $warning = is_scalar($warning) ? trim((string) $warning) : '';

        if ($persist) {
            StatusRepository::markDocumentSuccess(
                $type,
                $languages,
                $changed,
                $warning,
                $source,
                $document->getModifiedAt()
            );
        }

        ApiRequestLogger::succeeded(
            'document.fetch',
            $this->credentialMode,
            $response['code'],
            array_merge($logContext, ['warning_present' => $warning !== ''])
        );

        return [
            'success' => true,
            'code' => $response['code'],
            'body' => $response['body'],
            'error' => 'no_error',
            'changed' => $changed,
            'warning' => $warning,
        ];
    }

    public function fetchMessage() : array
    {
        $handler = $this->getHandler();
        $logContext = ['language' => 'de'];
        ApiRequestLogger::started('message.fetch', $this->credentialMode, $logContext);

        try {
            $handler->getMessage('de');
        } catch (Throwable $exception) {
            ApiRequestLogger::exception('message.fetch', $this->credentialMode, $exception, $logContext);
            throw $exception;
        }

        $response = $this->getLastResponse();
        $success = (bool) $handler->isLastResponseSuccess();

        if ($success) {
            ApiRequestLogger::succeeded('message.fetch', $this->credentialMode, $response['code'], $logContext);
        } else {
            ApiRequestLogger::failed(
                'message.fetch',
                $this->credentialMode,
                $response['code'],
                'error_getting_message',
                $logContext
            );
        }

        return [
            'success' => $success,
            'code' => $response['code'],
            'body' => $response['data'],
            'error' => $handler->isLastResponseSuccess() ? 'no_error' : 'error_getting_message',
        ];
    }

    public function getLastResponse() : array
    {
        $response = $this->getHandler()->getResponse();

        return [
            'code' => $response ? $response->getCode() : null,
            'body' => $response ? $response->getBody() : null,
            'data' => $response ? $response->getBodyDataAsArray() : null,
        ];
    }

    private function fetchDocument(ApiHandler $handler, string $type)
    {
        switch ($type) {
            case 'imprint':
                return $handler->getImprint();
            case 'privacyPolicy':
                return $handler->getPrivacyPolicy();
            case 'privacyPolicySocialMedia':
                return $handler->getPrivacyPolicySocialMedia();
        }

        throw new InvalidArgumentException('Invalid eRecht24 document type.');
    }

    private function errorType(string $type) : string
    {
        return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $type));
    }

    /**
     * Returns true only when an existing local text changed.
     */
    private function updateText(string $name, string $lang, string $content) : bool
    {
        $text = Text::where('name', $name)->where('lang', $lang)->first();
        $changed = $text !== null && (string) $text->text !== $content;

        if ($text === null) {
            $text = new Text();
        }

        $text->name = $name;
        $text->lang = $lang;
        $text->text = $content;
        $text->save();

        return $changed;
    }
}
