<?php

namespace Settings\Store;

use Settings\Contracts\Setting;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;

class SingletonSettingStore implements SettingStore
{

    private array $settings = [];

    private array $groups = [];

    public function getByKey(string $settingClass): Setting
    {
        if($this->has($settingClass)) {
            return $this->settings[$settingClass];
        }
        throw new SettingNotRegistered($settingClass);
    }

    /**
     * @param array|Setting[] $settings
     * @param array $extraGroups
     */
    public function register(array $settings, array $extraGroups): void
    {
        foreach($settings as $setting) {
            $this->settings[$setting->key()] = $setting;
        }
    }

    public function registerGroup(string $key, ?string $title = null, ?string $description = null): void
    {
        $this->groups[$key] = [
            'key' => $key,
            'title' => $title,
            'description' => $description
        ];
    }

    public function has(string $settingClass): bool
    {
        return array_key_exists($settingClass, $this->settings);
    }
}
