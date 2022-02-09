<?php

namespace Settings\Loading;

use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;

class DisplayLoadedSettings
{

    private LoadedSettings $loadedSettings;
    private SettingService $settings;

    public function __construct(LoadedSettings $loadedSettings, SettingService $settings)
    {
        $this->loadedSettings = $loadedSettings;
        $this->settings = $settings;
    }

    public function toString(): string
    {
        return $this->namespace() . $this->settingsAsJs();
    }

    private function namespace(): string
    {
        return 'window.ESSettings=window.ESSettings||{};';
    }

    private function settingsAsJs(): string
    {
        return collect($this->loadedSettings->getLoadingSettings())
            ->map(fn($key) => sprintf('ESSettings.%s=%s;', $key, json_encode($this->settings->getValue($key))))
            ->join('');
    }

}
