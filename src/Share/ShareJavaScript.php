<?php

namespace Settings\Share;

use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;

class ShareJavaScript
{

    private LoadedSettings $loadedSettings;
    private SettingService $settings;
    private ESConfig $esConfig;

    public function __construct(LoadedSettings $loadedSettings, SettingService $settings, ESConfig $esConfig)
    {
        $this->loadedSettings = $loadedSettings;
        $this->settings = $settings;
        $this->esConfig = $esConfig;
    }

    public function toString(): string
    {
        return $this->namespace() . $this->settingsAsJs() . $this->configAsJs();
    }

    private function namespace(): string
    {
        return 'window.ESSettings=window.ESSettings||{};window.ESSettingsConfig=window.ESSettingsConfig||{};';
    }

    private function settingsAsJs(): string
    {
        return collect($this->loadedSettings->getLoadingSettings())
            ->map(fn($key) => sprintf('ESSettings.%s=%s;', $key, json_encode($this->settings->getValue($key))))
            ->join('');
    }

    public function configAsJs(): string
    {
        return collect($this->esConfig->getConfig())
            ->map(fn($value, $key) => sprintf('ESSettingsConfig.%s=%s;', $key, json_encode($value)))
            ->join('');
    }

}
