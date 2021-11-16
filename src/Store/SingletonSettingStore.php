<?php

namespace Settings\Store;

use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;

class SingletonSettingStore implements SettingStore
{

    private array $settings = [];

    private array $groups = [];

    public function getByKey(string $key): Setting
    {
        if($this->has($key)) {
            return $this->settings[$key];
        }
        throw new SettingNotRegistered($key);
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

    public function groupIsRegistered(string $groupKey): bool
    {
        return array_key_exists($groupKey, $this->groups);
    }

    public function getGroupTitle(string $groupKey): ?string
    {
        if($this->groupIsRegistered($groupKey)) {
            return $this->groups[$groupKey]['title'];
        }
        return null;
    }

    public function getGroupSubtitle(string $groupKey): ?string
    {
        if($this->groupIsRegistered($groupKey)) {
            return $this->groups[$groupKey]['subtitle'];
        }
        return null;
    }

    public function registerGroup(string $key, ?string $title = null, ?string $subtitle = null): void
    {
        $this->groups[$key] = [
            'title' => $title,
            'subtitle' => $subtitle
        ];
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->settings);
    }

    public function all(): SettingCollection
    {
        return new SettingCollection($this->settings);
    }
}
