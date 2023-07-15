<?php

declare(strict_types=1);

namespace Xitara\ERecht24\Classes;

use Xitara\ERecht24\Models\Settings;
use eRecht24\RechtstexteSDK\ApiHandler;
use Input;
use Log;
use Xitara\ERecht24\Models\Text;

class ApiClient
{
    /**
     * @var string The developer-key
     */
    private $pluginKey = 'bvC59XwtUueJHtFyHu7EenvQNdFCq2yM9ZnxDuHJrCgxx2HmedKVJE3ckfvvhJX2';

    private $apiHandler;

    public function __construct()
    {
        /*
         * get api-key
         */
        $apiKey = Settings::get('apikey', null);
        if ($apiKey === null || $apiKey == '') {
            Log::critical('No api-key found. Aborting');
            return;
        }

        /*
         * initialize api handler
         */
        $this->apiHandler = new ApiHandler($apiKey, $this->pluginKey);
    }

    /**
     * return api-handler object
     *
     * @autor   mburghammer
     * @date    2023-06-27T17:17:13+02:00
     * @version 0.0.1
     * @since   0.0.1
     * @return  ApiHandler      api-handler object
     */
    public function getHandler(): ApiHandler
    {
        return $this->apiHandler;
    }

    public function testApiKey()
    {
        $apiKey = Input::get('Settings.apikey');

        if ($apiKey === null) {
            return [
                'success' => false,
                'code'    => null,
                'body'    => null,
                'error'   => 'no_api_key_found_in_post_data',
            ];
        }

        $this->apiHandler = new ApiHandler($apiKey, $this->pluginKey);

        $result = $this->getImprint('de');

        return [
            'success' => $result['success'],
            'code'    => '',
            'body'    => '',
            'error'   => ($result['success'] === false) ? 'no_response_or_wrong_api_key' : '',
        ];
    }

    /**
     * get imprint for given language
     *
     * @autor   mburghammer
     * @date    2023-06-27T17:40:26+02:00
     * @version 0.0.1
     * @since   0.0.1
     * @param   string $lang language-code, default "de"
     */
    public static function imprint($lang = 'de') {
        return (new self)->getImprint($lang);
    }

    private function getImprint($lang): array
    {
        $imprint = $this->apiHandler->getImprint();
        if (!$this->apiHandler->isLastResponseSuccess()) {
            return [
                'success' => false,
                'code'    => $this->apiHandler->getResponseCode(),
                'body'    => $this->apiHandler->getResponseBody(),
                'error'   => 'error_getting_imprint',
            ];
        }

        $html = $imprint->getHtml($lang);
        $this->updateText('imprint', $lang, $html);

        return [
            'success' => true,
            'code'    => $this->apiHandler->getResponseCode(),
            'body'    => $this->apiHandler->getResponseBody(),
            'error'   => 'no_error',
        ];
    }

    /**
     * get private policy for given language
     *
     * @autor   mburghammer
     * @date    2023-06-27T17:40:26+02:00
     * @version 0.0.1
     * @since   0.0.1
     * @param   string $lang language-code, default "de"
     */
    public static function privacyPolicy($lang = 'de') {
        return (new self)->getPrivacyPolicy($lang);
    }

    private function getPrivacyPolicy($lang): array
    {
        $privacy = $this->apiHandler->getPrivacyPolicy();
        if (!$this->apiHandler->isLastResponseSuccess()) {
            return [
                'success' => false,
                'code'    => $this->apiHandler->getResponseCode(),
                'body'    => $this->apiHandler->getResponseBody(),
                'error'   => 'error_getting_privacy_policy',
            ];
        }

        $html = $privacy->getHtml($lang);
        $this->updateText('privacyPolicy', $lang, $html);

        return [
            'success' => true,
            'code'    => $this->apiHandler->getResponseCode(),
            'body'    => $this->apiHandler->getResponseBody(),
            'error'   => 'no_error',
        ];
    }

    /**
     * get private policy social media for given language
     *
     * @autor   mburghammer
     * @date    2023-06-27T17:40:26+02:00
     * @version 0.0.1
     * @since   0.0.1
     * @param   string $lang language-code, default "de"
     */
    public static function privacyPolicySocialMedia($lang = 'de') {
        return (new self)->getPrivacyPolicySocialMedia($lang);
    }

    private function getPrivacyPolicySocialMedia($lang): array
    {
        $privacy = $this->apiHandler->getPrivacyPolicySocialMedia();
        if (!$this->apiHandler->isLastResponseSuccess()) {
            return [
                'success' => false,
                'code'    => $this->apiHandler->getResponseCode(),
                'body'    => $this->apiHandler->getResponseBody(),
                'error'   => 'error_getting_privacy_policy_social_media',
            ];
        }

        $html = $privacy->getHtml($lang);
        $this->updateText('privacyPolicySocialMedia', $lang, $html);

        return [
            'success' => true,
            'code'    => $this->apiHandler->getResponseCode(),
            'body'    => $this->apiHandler->getResponseBody(),
            'error'   => 'no_error',
        ];
    }

    private function updateText(string $name, string $lang, string $content): void
    {
        $text = Text::where('name', $name)->where('lang', $lang)->first();

        if ($text === null) {
            $text = new Text();
        }

        Log::debug($name);
        Log::debug($lang);
        Log::debug($content);

        $text->name = $name;
        $text->lang = $lang;
        $text->text = $content;
        $text->save();
    }
}
