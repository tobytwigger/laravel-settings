<?php

namespace Settings;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static void register(\Settings\Contracts\Setting|array $settings, array $extraGroups = []) Register new settings
 * @method static void registerGroup(string $key, string $title, ?string $description) Register information about a group
 * @method static mixed getValue(string $settingClass, ?int $id = null) Get the value of a setting
 * @method static void setDefaultValue(string $settingClass, mixed $value) Set the default value of a setting
 * @method static void setValue(string $settingClass, mixed $value, ?int $id = null) Set the value of a setting
 *
 * @see \Settings\Contracts\SettingService
 */
class Setting extends Facade
{

    /**
     * Retrieve the key the setting service is bound to
     *
     * @return string The facade accessor
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-settings';
    }

}
