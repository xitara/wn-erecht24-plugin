<?php

declare(strict_types=1);

namespace Xitara\ERecht24\Classes;

use eRecht24\RechtstexteSDK\Model\Client;
use InvalidArgumentException;
use System\Behaviors\SettingsModel as SettingsBehavior;
use Throwable;
use Xitara\ERecht24\Models\Settings;

class PushClientManager
{
    private const MAX_CLIENTS_PER_PROJECT = 3;
    private const TEST_TYPES = [
        'ping',
        'message',
        'imprint',
        'privacyPolicy',
        'privacyPolicySocialMedia',
    ];

    public function register(?string $credentialMode = null) : array
    {
        $uri = $this->validateUri(Settings::getPushUri());
        $credentialMode = $this->credentialMode($credentialMode);
        $clientId = (int) Settings::get('push_client_id', 0);
        $registeredMode = (string) Settings::get(
            'push_client_credential_mode',
            $clientId > 0 ? ApiClient::CREDENTIAL_MODE_CONFIGURED : ''
        );

        if ($clientId > 0 && $registeredMode !== '' && $registeredMode !== $credentialMode) {
            return $this->withClientsText([
                'success' => false,
                'message' => trans('xitara.erecht24::lang.settings.push_actions.mode_changed'),
            ], $credentialMode);
        }

        $api = ApiClient::forCredentialMode($credentialMode);
        $handler = $api->getHandler();
        $clientList = $this->loadClients($api, $credentialMode);
        if (!$clientList['success']) {
            return $this->withClientsText($clientList, $credentialMode);
        }

        $clients = $clientList['clients'];
        if ($clientId === 0) {
            $matchingClient = $this->findMatchingClient($clients, $uri);
            $clientId = $matchingClient ? (int) $matchingClient->getClientId() : 0;
        }

        $client = $this->makeClient($uri);
        if ($clientId > 0) {
            $client->setClientId($clientId);
            $client = $this->apiCall(
                'clients.update',
                $api,
                $credentialMode,
                function () use ($handler, $client) {
                    return $handler->updateClient($client);
                },
                ['client_id' => $clientId]
            );
        } else {
            if (count($clients) >= self::MAX_CLIENTS_PER_PROJECT) {
                return $this->clientLimitFailure($credentialMode, count($clients));
            }

            $client = $this->apiCall(
                'clients.create',
                $api,
                $credentialMode,
                function () use ($handler, $client) {
                    return $handler->createClient($client);
                }
            );
        }

        if (!$handler->isLastResponseSuccess()) {
            return $this->withClientsText(
                $this->failure($api, 'Client registration failed.'),
                $credentialMode
            );
        }

        Settings::set([
            'push_client_id' => $client->getClientId() ?: $clientId,
            'push_secret' => $client->getSecret(),
            'is_push_active' => true,
            'push_registered_at' => date('c'),
            'push_client_credential_mode' => $credentialMode,
        ]);

        $this->storeClients($this->replaceClient($clients, $client), $credentialMode);

        return $this->withClientsText([
            'success' => true,
            'message' => trans('xitara.erecht24::lang.settings.push_actions.registered'),
        ], $credentialMode);
    }

    public function unregister() : array
    {
        $clientId = (int) Settings::get('push_client_id', 0);
        $credentialMode = ApiClient::normalizeCredentialMode((string) Settings::get(
            'push_client_credential_mode',
            ApiClient::CREDENTIAL_MODE_CONFIGURED
        ));

        if ($clientId > 0) {
            $api = ApiClient::forCredentialMode($credentialMode);
            $handler = $api->getHandler();
            $deleted = $this->apiCall(
                'clients.delete',
                $api,
                $credentialMode,
                function () use ($handler, $clientId) {
                    return $handler->deleteClient($clientId);
                },
                ['client_id' => $clientId]
            );
            $response = $api->getLastResponse();

            if (!$deleted && (int) $response['code'] !== 404) {
                return $this->withClientsText(
                    $this->failure($api, 'Client could not be removed.'),
                    $credentialMode
                );
            }
        }

        $this->clearLocalRegistration();
        $this->removeCachedClient($clientId, $credentialMode);

        return $this->withClientsText([
            'success' => true,
            'message' => trans('xitara.erecht24::lang.settings.push_actions.unregistered'),
        ], $credentialMode);
    }

    public function refreshClients(?string $credentialMode = null) : array
    {
        $credentialMode = $this->credentialMode($credentialMode);
        $api = ApiClient::forCredentialMode($credentialMode);
        $result = $this->loadClients($api, $credentialMode);

        if (!$result['success']) {
            return $this->withClientsText($result, $credentialMode);
        }

        return $this->withClientsText([
            'success' => true,
            'message' => trans('xitara.erecht24::lang.settings.push_actions.clients_refreshed', [
                'count' => count($result['clients']),
            ]),
            'clients' => $result['clients'],
        ], $credentialMode);
    }

    public function unregisterAll(?string $credentialMode = null) : array
    {
        $credentialMode = $this->credentialMode($credentialMode);
        $api = ApiClient::forCredentialMode($credentialMode);
        $handler = $api->getHandler();
        $clientList = $this->loadClients($api, $credentialMode);
        if (!$clientList['success']) {
            return $this->withClientsText($clientList, $credentialMode);
        }

        $remainingClients = [];
        $deletedCount = 0;

        foreach ($clientList['clients'] as $client) {
            $clientId = (int) $client->getClientId();

            try {
                $deleted = $this->apiCall(
                    'clients.delete',
                    $api,
                    $credentialMode,
                    function () use ($handler, $clientId) {
                        return $handler->deleteClient($clientId);
                    },
                    ['client_id' => $clientId]
                );
                $response = $api->getLastResponse();

                if ($deleted || (int) $response['code'] === 404) {
                    $deletedCount++;
                    continue;
                }
            } catch (Throwable $exception) {
                // The API call is already logged; retain the client in the displayed list.
            }

            $remainingClients[] = $client;
        }

        $this->storeClients($remainingClients, $credentialMode);
        $this->clearLocalRegistrationWhenMissing($remainingClients, $credentialMode);

        if ($remainingClients !== []) {
            return $this->withClientsText([
                'success' => false,
                'message' => trans('xitara.erecht24::lang.settings.push_actions.unregister_all_partial', [
                    'deleted' => $deletedCount,
                    'remaining' => count($remainingClients),
                ]),
            ], $credentialMode);
        }

        return $this->withClientsText([
            'success' => true,
            'message' => trans('xitara.erecht24::lang.settings.push_actions.unregister_all_success', [
                'count' => $deletedCount,
            ]),
        ], $credentialMode);
    }

    public function test(string $type, string $uri, ?string $credentialMode = null) : array
    {
        if (!in_array($type, self::TEST_TYPES, true)) {
            throw new InvalidArgumentException('Invalid test push type.');
        }

        $uri = $this->validateUri($uri);
        $credentialMode = $this->credentialMode($credentialMode);
        $api = ApiClient::forCredentialMode($credentialMode);
        $handler = $api->getHandler();
        $clientList = $this->loadClients($api, $credentialMode);
        if (!$clientList['success']) {
            return $this->withClientsText($clientList, $credentialMode);
        }

        $clients = $clientList['clients'];
        $testClient = $this->findReusableTestClient($clients, $uri, $credentialMode);
        $temporaryClient = $testClient === null;

        if ($temporaryClient && count($clients) >= self::MAX_CLIENTS_PER_PROJECT) {
            return $this->clientLimitFailure($credentialMode, count($clients));
        }

        $testClientId = null;

        try {
            if ($temporaryClient) {
                $testClient = $this->makeClient($uri);
                $testClient = $this->apiCall(
                    'clients.create_test',
                    $api,
                    $credentialMode,
                    function () use ($handler, $testClient) {
                        return $handler->createClient($testClient);
                    }
                );

                if (!$handler->isLastResponseSuccess()) {
                    return $this->withClientsText(
                        $this->failure($api, 'Test client registration failed.'),
                        $credentialMode
                    );
                }
            }

            $testClientId = $testClient->getClientId();
            $testSecret = $temporaryClient
                ? $testClient->getSecret()
                : (string) Settings::get('push_secret', '');
            Settings::set([
                'push_test_client_id' => $testClientId,
                'push_test_secret' => $testSecret,
                'push_test_credential_mode' => $credentialMode,
                'push_test_result' => [
                    'type' => $type,
                    'success' => false,
                    'message' => 'Waiting for callback.',
                    'tested_at' => date('c'),
                ],
            ]);

            $success = $this->apiCall(
                'clients.test_push',
                $api,
                $credentialMode,
                function () use ($handler, $testClientId, $type) {
                    return $handler->fireTestPush((int) $testClientId, $type);
                },
                [
                    'client_id' => $testClientId,
                    'push_type' => $type,
                    'temporary_client' => $temporaryClient,
                ]
            );
            if (!$success) {
                return $this->withClientsText(
                    $this->failure($api, 'The eRecht24 testPush endpoint reported an error.'),
                    $credentialMode
                );
            }

            SettingsBehavior::clearInternalCache();
            $callbackResult = Settings::get('push_test_result', []);

            return $this->withClientsText([
                'success' => is_array($callbackResult) && !empty($callbackResult['success']),
                'message' => is_array($callbackResult)
                    ? (string) ($callbackResult['message'] ?? 'Test callback finished.')
                    : 'Test callback finished.',
            ], $credentialMode);
        } finally {
            $cleanupFailed = false;

            if ($temporaryClient && $testClientId) {
                try {
                    $deleted = $this->apiCall(
                        'clients.delete_test',
                        $api,
                        $credentialMode,
                        function () use ($handler, $testClientId) {
                            return $handler->deleteClient((int) $testClientId);
                        },
                        ['client_id' => $testClientId]
                    );
                    $response = $api->getLastResponse();
                    $cleanupFailed = !$deleted && (int) $response['code'] !== 404;
                } catch (Throwable $exception) {
                    $cleanupFailed = true;
                }
            }

            if ($cleanupFailed && $testClient instanceof Client) {
                $clients[] = $testClient;
                $this->storeClients($clients, $credentialMode);
            }

            Settings::set([
                'push_test_client_id' => null,
                'push_test_secret' => null,
                'push_test_credential_mode' => null,
            ]);
        }
    }

    private function loadClients(ApiClient $api, string $credentialMode) : array
    {
        $handler = $api->getHandler();
        $collection = $this->apiCall(
            'clients.list',
            $api,
            $credentialMode,
            function () use ($handler) {
                return $handler->getClientList();
            }
        );

        if (!$handler->isLastResponseSuccess()) {
            return $this->failure($api, 'Clients could not be read.');
        }

        $clients = $collection->all();
        $this->storeClients($clients, $credentialMode);

        return [
            'success' => true,
            'clients' => $clients,
        ];
    }

    private function apiCall(
        string $operation,
        ApiClient $api,
        string $credentialMode,
        callable $callback,
        array $context = []
    ) {
        ApiRequestLogger::started($operation, $credentialMode, $context);

        try {
            $result = $callback();
        } catch (Throwable $exception) {
            ApiRequestLogger::exception($operation, $credentialMode, $exception, $context);
            throw $exception;
        }

        $response = $api->getLastResponse();
        if ($api->getHandler()->isLastResponseSuccess()) {
            ApiRequestLogger::succeeded($operation, $credentialMode, $response['code'], $context);
        } else {
            ApiRequestLogger::failed(
                $operation,
                $credentialMode,
                $response['code'],
                'provider_error',
                $context
            );
        }

        return $result;
    }

    private function clientLimitFailure(string $credentialMode, int $clientCount) : array
    {
        ApiRequestLogger::skipped('clients.create', $credentialMode, 'client_limit_reached', [
            'client_count' => $clientCount,
            'client_limit' => self::MAX_CLIENTS_PER_PROJECT,
        ]);

        return $this->withClientsText([
            'success' => false,
            'message' => trans('xitara.erecht24::lang.settings.push_actions.client_limit', [
                'count' => $clientCount,
                'limit' => self::MAX_CLIENTS_PER_PROJECT,
            ]),
            'code' => 409,
        ], $credentialMode);
    }

    private function makeClient(string $uri) : Client
    {
        return (new Client())
            ->setPushMethod('POST')
            ->setPushUri($uri)
            ->setCms('Winter CMS')
            ->setPluginName('xitara/wn-erecht24-plugin')
            ->setAuthorMail('mb@xitara.com');
    }

    private function findMatchingClient(array $clients, string $uri) : ?Client
    {
        foreach ($clients as $client) {
            if (!$client instanceof Client) {
                continue;
            }

            if ($client->getPushUri() === $uri
                && $client->getPluginName() === 'xitara/wn-erecht24-plugin'
            ) {
                return $client;
            }
        }

        return null;
    }

    private function findReusableTestClient(
        array $clients,
        string $uri,
        string $credentialMode
    ) : ?Client {
        $registeredId = (int) Settings::get('push_client_id', 0);
        $registeredMode = ApiClient::normalizeCredentialMode((string) Settings::get(
            'push_client_credential_mode',
            ApiClient::CREDENTIAL_MODE_CONFIGURED
        ));

        if ($registeredId === 0
            || $registeredMode !== $credentialMode
            || (string) Settings::get('push_secret', '') === ''
        ) {
            return null;
        }

        foreach ($clients as $client) {
            if ($client instanceof Client
                && (int) $client->getClientId() === $registeredId
                && $client->getPushUri() === $uri
            ) {
                return $client;
            }
        }

        return null;
    }

    private function replaceClient(array $clients, Client $replacement) : array
    {
        $replaced = false;

        foreach ($clients as $index => $client) {
            if ($client instanceof Client && $client->getClientId() === $replacement->getClientId()) {
                $clients[$index] = $replacement;
                $replaced = true;
                break;
            }
        }

        if (!$replaced) {
            $clients[] = $replacement;
        }

        return array_values($clients);
    }

    private function storeClients(array $clients, string $credentialMode) : void
    {
        $serialized = [];

        foreach ($clients as $client) {
            if (!$client instanceof Client) {
                continue;
            }

            $serialized[] = [
                'client_id' => $client->getClientId(),
                'push_method' => $client->getPushMethod(),
                'push_uri' => $client->getPushUri(),
                'cms' => $client->getCms(),
                'plugin_name' => $client->getPluginName(),
                'created_at' => $client->getCreatedAt(),
                'updated_at' => $client->getUpdatedAt(),
            ];
        }

        Settings::set([
            'push_clients' => $serialized,
            'push_clients_credential_mode' => $credentialMode,
            'push_clients_refreshed_at' => date('c'),
        ]);
    }

    private function removeCachedClient(int $clientId, string $credentialMode) : void
    {
        if ((string) Settings::get('push_clients_credential_mode', '') !== $credentialMode) {
            return;
        }

        $clients = array_values(array_filter(
            (array) Settings::get('push_clients', []),
            function ($client) use ($clientId) {
                return !is_array($client) || (int) ($client['client_id'] ?? 0) !== $clientId;
            }
        ));

        Settings::set([
            'push_clients' => $clients,
            'push_clients_refreshed_at' => date('c'),
        ]);
    }

    private function clearLocalRegistrationWhenMissing(array $clients, string $credentialMode) : void
    {
        $registeredMode = ApiClient::normalizeCredentialMode((string) Settings::get(
            'push_client_credential_mode',
            ApiClient::CREDENTIAL_MODE_CONFIGURED
        ));

        if ($registeredMode !== $credentialMode) {
            return;
        }

        $registeredId = (int) Settings::get('push_client_id', 0);
        foreach ($clients as $client) {
            if ($client instanceof Client && (int) $client->getClientId() === $registeredId) {
                return;
            }
        }

        $this->clearLocalRegistration();
    }

    private function clearLocalRegistration() : void
    {
        Settings::set([
            'push_client_id' => null,
            'push_secret' => null,
            'is_push_active' => false,
            'push_registered_at' => null,
            'push_client_credential_mode' => null,
        ]);
    }

    private function credentialMode(?string $credentialMode) : string
    {
        return ApiClient::normalizeCredentialMode(
            $credentialMode ?: ApiClient::getCredentialMode()
        );
    }

    private function validateUri(string $uri) : string
    {
        $uri = trim($uri);
        $scheme = strtolower((string) parse_url($uri, PHP_URL_SCHEME));

        if (!filter_var($uri, FILTER_VALIDATE_URL) || !in_array($scheme, ['http', 'https'], true)) {
            throw new InvalidArgumentException('A valid HTTP(S) push URL is required.');
        }

        return $uri;
    }

    private function failure(ApiClient $api, string $fallback) : array
    {
        $response = $api->getLastResponse();
        $data = is_array($response['data']) ? $response['data'] : [];

        return [
            'success' => false,
            'message' => (string) ($data['message_de'] ?? $data['message'] ?? $fallback),
            'code' => $response['code'],
        ];
    }

    private function withClientsText(array $result, string $credentialMode) : array
    {
        $result['clients_text'] = Settings::getRegisteredClientsDisplay($credentialMode);

        return $result;
    }
}
