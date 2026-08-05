<?php

namespace Xitara\ERecht24\Models;

use Model;
use Url;

/**
 * Settings Model
 *
 * @property int                                            $id
 * @property string|null                                    $item
 * @property string|null                                    $value
 * @method static \Winter\Storm\Database\Collection<int, static> all($columns = ['*'])
 * @method static \Winter\Storm\Database\Collection<int, static> get($columns = ['*'])
 * @method static \Winter\Storm\Database\Builder|Settings        lists(string $column, string $key = null)
 * @method static \Winter\Storm\Database\Builder|Settings        newModelQuery()
 * @method static \Winter\Storm\Database\Builder|Settings        newQuery()
 * @method static \Winter\Storm\Database\Builder|Settings        orSearchWhere(string $term, string $columns = [], string $mode = 'all')
 * @method static \Winter\Storm\Database\Builder|Settings        query()
 * @method static \Winter\Storm\Database\Builder|Settings        searchWhere(string $term, string $columns = [], string $mode = 'all')
 * @method static \Winter\Storm\Database\Builder|Settings        whereId($value)
 * @method static \Winter\Storm\Database\Builder|Settings        whereItem($value)
 * @method static \Winter\Storm\Database\Builder|Settings        whereValue($value)
 * @mixin \Eloquent
 */
class Settings extends Model
{
    use \Winter\Storm\Database\Traits\Validation;

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'xitara_erecht24_settings';
    public $settingsFields = 'fields.yaml';

    public $rules = [
        'push_uri' => 'nullable|url',
    ];

    public function initSettingsData() : void
    {
        $this->use_demo_credentials = false;
        $this->interval = 'weekly';
        $this->is_pull_active = false;
        $this->is_push_active = false;
        $this->push_uri = Url::to('/api/erecht24/push');
        $this->push_test_type = 'ping';
    }

    public function getPushUriAttribute($value) : string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : Url::to('/api/erecht24/push');
    }

    public static function getPushUri() : string
    {
        $value = trim((string) self::get('push_uri', ''));

        return $value !== '' ? $value : Url::to('/api/erecht24/push');
    }

    public function getRegisteredClientsAttribute($value) : string
    {
        return self::getRegisteredClientsDisplay();
    }

    public static function getRegisteredClientsDisplay(?string $credentialMode = null) : string
    {
        $credentialMode = $credentialMode ?: (
            self::get('use_demo_credentials', false) ? 'demo' : 'configured'
        );
        $credentialMode = $credentialMode === 'demo' ? 'demo' : 'configured';
        $cachedMode = (string) self::get('push_clients_credential_mode', '');
        $modeLabel = trans('xitara.erecht24::lang.settings.credential_modes.' . $credentialMode);

        if ($cachedMode !== $credentialMode) {
            return trans('xitara.erecht24::lang.settings.registered_clients.not_loaded', [
                'mode' => $modeLabel,
            ]);
        }

        $clients = (array) self::get('push_clients', []);
        if ($clients === []) {
            return trans('xitara.erecht24::lang.settings.registered_clients.none', [
                'mode' => $modeLabel,
            ]);
        }

        $lines = [trans('xitara.erecht24::lang.settings.registered_clients.summary', [
            'count' => count($clients),
            'limit' => 3,
            'mode' => $modeLabel,
        ])];

        foreach ($clients as $client) {
            if (!is_array($client)) {
                continue;
            }

            $lines[] = trans('xitara.erecht24::lang.settings.registered_clients.line', [
                'id' => (int) ($client['client_id'] ?? 0),
                'method' => self::singleLine($client['push_method'] ?? ''),
                'uri' => self::singleLine($client['push_uri'] ?? ''),
                'cms' => self::singleLine($client['cms'] ?? ''),
                'plugin' => self::singleLine($client['plugin_name'] ?? ''),
            ]);
        }

        return implode(PHP_EOL, $lines);
    }

    private static function singleLine($value) : string
    {
        return trim((string) preg_replace('/\s+/', ' ', (string) $value));
    }
}
