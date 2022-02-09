<?php

namespace Settings\Loading;

use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;

class LoadedSettings
{

    private array $loadingKeys = [];
    private SettingStore $settingStore;

    public function __construct(SettingStore $settingStore)
    {
        $this->settingStore = $settingStore;
    }

    public function load(string $key): void
    {
        if(!$this->settingStore->has($key)) {
            throw new SettingNotRegistered($key);
        }
        if(!in_array($key, $this->loadingKeys)) {
            $this->loadingKeys[] = $key;
        }
    }

    public function loadMany(array $keys): void
    {
        foreach($keys as $key) {
            if(!$this->settingStore->has($key)) {
                throw new SettingNotRegistered($key);
            }
        }
        $this->loadingKeys = array_unique(array_merge(
            $this->loadingKeys,
            $keys
        ));
    }

    public function getLoadingSettings(): array
    {
        return $this->loadingKeys;
    }

}
