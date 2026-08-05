<?php

declare(strict_types=1);

namespace Xitara\ERecht24\Classes;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Xitara\ERecht24\Models\Settings;

class PushService
{
    private const DOCUMENT_TYPES = [
        'imprint',
        'privacyPolicy',
        'privacyPolicySocialMedia',
    ];
    private const PUSH_TYPES = [
        'ping',
        'message',
        'imprint',
        'privacyPolicy',
        'privacyPolicySocialMedia',
    ];

    public function handle(Request $request) : JsonResponse
    {
        $secret = trim((string) $request->input('erecht24_secret', ''));
        $type = trim((string) $request->input('erecht24_type', ''));
        $isTest = $this->isTestRequest($secret, $type);
        $isProduction = $this->isProductionRequest($secret);

        Log::debug('eRecht24 push request received.', [
            'push_type' => $type,
            'test_authenticated' => $isTest,
            'production_authenticated' => $isProduction,
        ]);

        if (!$isTest && !$isProduction) {
            Log::warning('eRecht24 push request rejected.', [
                'push_type' => $type,
                'reason' => 'authentication_failed',
            ]);

            return response()->json(['message' => 'Unauthorized request.'], 401);
        }

        if (!in_array($type, self::PUSH_TYPES, true)) {
            Log::warning('eRecht24 push request rejected.', [
                'push_type' => $type,
                'reason' => 'invalid_push_type',
            ]);

            return response()->json(['message' => 'Invalid push type.'], 400);
        }

        if ($type === 'ping') {
            if ($isTest) {
                StatusRepository::recordTestResult($type, true, 'pong');
            }

            Log::info('eRecht24 ping push processed.', [
                'test_request' => $isTest,
            ]);

            return response()->json(['message' => 'pong'], 200);
        }

        try {
            $apiClient = $isTest
                ? ApiClient::forCredentialMode((string) Settings::get(
                    'push_test_credential_mode',
                    ApiClient::CREDENTIAL_MODE_DEMO
                ))
                : ApiClient::forCredentialMode((string) Settings::get(
                    'push_client_credential_mode',
                    ApiClient::CREDENTIAL_MODE_CONFIGURED
                ));

            if ($type === 'message') {
                return $this->handleMessage($apiClient, $isTest);
            }

            if (!$isTest && !in_array($type, (array) Settings::get('docs', []), true)) {
                return response()->json(['message' => 'Document is managed locally.'], 422);
            }

            $languages = $isTest ? ['de', 'en'] : (array) Settings::get('langs', []);
            $result = $apiClient->importDocument($type, $languages, 'push', !$isTest);

            if ($isTest) {
                StatusRepository::recordTestResult(
                    $type,
                    $result['success'],
                    $result['success'] ? 'Test document fetched.' : 'Test document fetch failed.'
                );
            }

            if (!$result['success']) {
                return response()->json(['message' => 'Legal text could not be updated.'], 400);
            }

            return response()->json(['message' => 'Legal text updated.', 'type' => $type], 200);
        } catch (Throwable $exception) {
            Log::error('eRecht24 push processing failed.', [
                'push_type' => $type,
                'test_request' => $isTest,
                'exception_class' => get_class($exception),
            ]);

            if ($isTest) {
                StatusRepository::recordTestResult($type, false, $exception->getMessage());
            } elseif (in_array($type, self::DOCUMENT_TYPES, true)) {
                StatusRepository::markDocumentError(
                    $type,
                    (array) Settings::get('langs', []),
                    'push_processing_failed',
                    'push'
                );
            }

            return response()->json(['message' => 'Push notification could not be processed.'], 400);
        }
    }

    private function handleMessage(ApiClient $apiClient, bool $isTest) : JsonResponse
    {
        $result = $apiClient->fetchMessage();

        if (!$result['success']) {
            if ($isTest) {
                StatusRepository::recordTestResult('message', false, 'Test message fetch failed.');
            }

            return response()->json(['message' => 'Message could not be fetched.'], 400);
        }

        if ($isTest) {
            StatusRepository::recordTestResult('message', true, 'Test message fetched.');
        } elseif ((int) $result['code'] === 204 || !is_array($result['body'])) {
            StatusRepository::clearMessage();
        } else {
            StatusRepository::recordMessage($result['body']);
        }

        return response()->json(['message' => 'Message processed.'], 200);
    }

    private function isTestRequest(string $secret, string $type) : bool
    {
        $testSecret = (string) Settings::get('push_test_secret', '');
        $testResult = Settings::get('push_test_result', []);
        $testType = is_array($testResult) ? (string) ($testResult['type'] ?? '') : '';

        return $secret !== ''
            && $testSecret !== ''
            && $testType !== ''
            && hash_equals($testSecret, $secret)
            && hash_equals($testType, $type);
    }

    private function isProductionRequest(string $secret) : bool
    {
        if (!Settings::get('is_push_active', false)) {
            return false;
        }

        $productionSecret = (string) Settings::get('push_secret', '');

        return $secret !== '' && $productionSecret !== '' && hash_equals($productionSecret, $secret);
    }
}
