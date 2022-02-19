<?php

namespace Settings\Anonymous;

use FormSchema\Schema\Field;
use Settings\Contracts\Setting;
use Settings\Types\GlobalSetting;

class AnonymousSettingFactory
{

    private static array $typeMap = [];

    public static function mapType(string $alias, \Closure $callback)
    {
        static::$typeMap[$alias] = $callback;
    }

    public static function anonymous(string $type, string $key, mixed $defaultValue, ?Field $fieldOptions = null, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): AnonymousSetting
    {
        return static::create($type, $key, $defaultValue, $fieldOptions, $groups, $rules, $resolveIdUsing);
    }

    public static function create(string $type, string $key, mixed $defaultValue, ?Field $fieldOptions = null, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): AnonymousSetting
    {
        $setting = static::make($type, $key, $defaultValue, $fieldOptions, $groups, $rules, $resolveIdUsing);

        \Settings\Setting::register($setting);

        return $setting;
    }

    public static function make(string $type, string $key, mixed $defaultValue, ?Field $fieldOptions = null, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): AnonymousSetting
    {
        if($resolveIdUsing === null) {
            $resolveIdUsing = array_key_exists($type, static::$typeMap) ? static::$typeMap[$type] : throw new \Exception(sprintf('Anonymous setting type [%s] was not found.', $type));
        }

        return new AnonymousSetting($type, $key, $defaultValue, $resolveIdUsing, $fieldOptions, $groups, $rules);
    }


}
