<?php

namespace Xitara\ERecht24;

use App;
use Artisan;
use Backend;
use Backend\Models\UserRole;
use Flash;
use Input;
use System\Classes\PluginBase;
use System\Classes\PluginManager;
use System\Controllers\Settings as SettingsController;
use Xitara\ERecht24\Classes\ApiClient;
use Xitara\ERecht24\Classes\PushClientManager;
use Xitara\ERecht24\Models\Settings;

/**
 * eRecht24 Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     */
    public function pluginDetails() : array
    {
        return [
            'name' => 'xitara.erecht24::lang.plugin.name',
            'description' => 'xitara.erecht24::lang.plugin.description',
            'author' => 'xitara.erecht24::lang.plugin.author',
            'icon' => 'xitara.erecht24::lang.plugin.icon',
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register() : void
    {
        $this->registerConsoleCommand('erecht.import', \Xitara\ERecht24\Console\Import::class);
    }

    /**
     * Boot method, called right before the request route.
     */
    public function boot() : void
    {
        if (!App::runningInBackend()) {
            return;
        }

        SettingsController::extend(function ($controller) {
            $controller->addDynamicMethod('onTestApiKey', function () {
                $result = (new ApiClient())->testApiKey();

                if ($result['success'] === true) {
                    Flash::success(e(trans('xitara.erecht24::lang.flash.success')));

                    return;
                }

                Flash::error(
                    e(trans('xitara.erecht24::lang.flash.error')) .
                    ': ' . e(trans('xitara.erecht24::lang.error.' . $result['error']))
                );
            });

            $controller->addDynamicMethod('onPullNow', function () {
                try {
                    Artisan::call('erecht:import');

                    Flash::success(e(trans('xitara.erecht24::lang.flash.success')));
                } catch (\Exception $e) {
                    Flash::error(e(trans('xitara.erecht24::lang.flash.error')));
                }
            });

            $controller->addDynamicMethod('onRegisterPush', function () {
                try {
                    $result = (new PushClientManager())->register(
                        ApiClient::getSubmittedCredentialMode()
                    );
                    $this->flashPushResult($result);

                    return ['clients_text' => $result['clients_text'] ?? ''];
                } catch (\Throwable $exception) {
                    Flash::error(e($exception->getMessage()));
                }
            });

            $controller->addDynamicMethod('onUnregisterPush', function () {
                try {
                    $result = (new PushClientManager())->unregister();
                    $this->flashPushResult($result);

                    return ['clients_text' => $result['clients_text'] ?? ''];
                } catch (\Throwable $exception) {
                    Flash::error(e($exception->getMessage()));
                }
            });

            $controller->addDynamicMethod('onTestPush', function () {
                try {
                    $type = (string) Input::get('Settings.push_test_type', 'ping');
                    $uri = (string) Input::get(
                        'Settings.push_uri',
                        Settings::getPushUri()
                    );
                    $result = (new PushClientManager())->test(
                        $type,
                        $uri,
                        ApiClient::getSubmittedCredentialMode()
                    );
                    $this->flashPushResult($result);

                    return ['clients_text' => $result['clients_text'] ?? ''];
                } catch (\Throwable $exception) {
                    Flash::error(e($exception->getMessage()));
                }
            });

            $controller->addDynamicMethod('onRefreshPushClients', function () {
                try {
                    $result = (new PushClientManager())->refreshClients(
                        ApiClient::getSubmittedCredentialMode()
                    );
                    $this->flashPushResult($result);

                    return ['clients_text' => $result['clients_text'] ?? ''];
                } catch (\Throwable $exception) {
                    Flash::error(e($exception->getMessage()));
                }
            });

            $controller->addDynamicMethod('onUnregisterAllPushClients', function () {
                try {
                    $result = (new PushClientManager())->unregisterAll(
                        ApiClient::getSubmittedCredentialMode()
                    );
                    $this->flashPushResult($result);

                    return ['clients_text' => $result['clients_text'] ?? ''];
                } catch (\Throwable $exception) {
                    Flash::error(e($exception->getMessage()));
                }
            });
        });
    }

    /**
     * Registers any frontend components implemented in this plugin.
     */
    public function registerComponents() : array
    {
        return [
            'Xitara\ERecht24\Components\Output' => 'output',
        ];
    }
    public function registerPageSnippets() : array
    {
        return [
            'Xitara\ERecht24\Components\Output' => 'output',
        ];
    }

    public function registerReportWidgets() : array
    {
        return [
            'Xitara\ERecht24\ReportWidgets\LegalTextStatus' => [
                'label' => 'xitara.erecht24::lang.dashboard.widget_title',
                'context' => 'dashboard',
            ],
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     */
    public function registerPermissions() : array
    {
        return [];

        // Remove this line to activate
        return [
            'xitara.erecht24.some_permission' => [
                'tab' => 'xitara.erecht24::lang.plugin.name',
                'label' => 'xitara.erecht24::lang.permissions.some_permission',
                'roles' => [
                    UserRole::CODE_DEVELOPER,
                    UserRole::CODE_PUBLISHER,
                ],
            ],
        ];
    }

    /**
     * Registers backend navigation items for this plugin.
     */
    public function registerNavigation() : array
    {
        return [];

        // Remove this line to activate
        return [
            'erecht24' => [
                'label' => 'xitara.erecht24::lang.plugin.name',
                'url' => Backend::url('xitara/erecht24/mycontroller'),
                'icon' => 'icon-leaf',
                'permissions' => ['xitara.erecht24.*'],
                'order' => 500,
            ],
        ];
    }

    public function registerSettings()
    {
        $category = 'xitara.erecht24::lang.settings.label';

        if (PluginManager::instance()->exists('Xitara.Nexus') === true) {
            if (($category = \Xitara\Nexus\Models\Settings::get('menu_text')) == '') {
                $category = 'xitara.nexus::core.settings.name';
            }
        }

        return [
            'settings' => [
                'category' => $category,
                'label' => 'xitara.erecht24::lang.submenu.label',
                'description' => 'xitara.erecht24::lang.submenu.description',
                'icon' => 'icon-balance-scale',
                'class' => 'Xitara\ERecht24\Models\Settings',
                'order' => 20,
            ],
        ];
    }

    public function registerSchedule($schedule)
    {
        switch (Settings::get('interval')) {
            case 'daily':
                $schedule->command('erecht:import')->daily();
                break;
            case 'weekly':
                $schedule->command('erecht:import')->weekly();
                break;
            case 'monthly':
                $schedule->command('erecht:import')->monthly();
                break;
            default:
                break;
        }
    }

    private function flashPushResult(array $result) : void
    {
        $message = e((string) ($result['message'] ?? trans('xitara.erecht24::lang.flash.error')));

        if (!empty($result['success'])) {
            Flash::success($message);

            return;
        }

        Flash::error($message);
    }
}
