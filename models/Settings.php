<?php namespace Xitara\ERecht24\Models;

use Model;

/**
 * Settings Model
 */
class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'xitara_erecht24_settings';
    public $settingsFields = 'fields.yaml';
}
