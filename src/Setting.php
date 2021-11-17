<?php

namespace Settings;

use FormSchema\Schema\Field;
use Illuminate\Support\Facades\Facade;
use Settings\Contracts\SettingStore;
use Settings\Store\Query;

/**
 *
 * @method static void register(\Settings\Contracts\Setting|array $settings, array $extraGroups = []) Register new settings
 * @method static void registerGroup(string $key, string $title, ?string $subtitle) Register information about a group
 * @method static mixed getValue(string $key, ?int $id = null) Get the value of a setting
 * @method static void setDefaultValue(string $key, mixed $value) Set the default value of a setting
 * @method static void setValue(string $key, mixed $value, ?int $id = null) Set the value of a setting
 * @method static Setting getSettingByKey(string $key) Get the setting class
 * @method static Query search() Start a query
 * @method static SettingStore store() Access the setting store directly
 * @method static Query withGroup(string $groupName) Only settings with the given group
 * @method static Query withAnyGroups(array $groups) Only settings with at least one of the given groups
 * @method static Query withAllGroups(array $groups) Only settings with all the given groups
 * @method static Query withType(string $type) Only settings of the given type
 * @method static Query withGlobalType() Only global settings
 * @method static Query withUserType() Only user settings
 * @method static void alias(string $alias, string $key) Alias the $key with $alias
 * @method static \Settings\Contracts\Setting create(string $type, string $key, mixed $defaultValue, Field $fieldOptions, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null) Create and register a new setting
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
