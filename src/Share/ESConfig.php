<?php

namespace Settings\Share;

use Illuminate\Contracts\Config\Repository;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;

class ESConfig
{


    private Repository $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    private function isApiEnabled(): bool
    {
        return $this->config->get('laravel-settings.routes.api.enabled', true);
    }

    private function getGetApiUrl(): ?string
    {
        return route('settings.get');
    }

    private function getUpdateApiUrl(): ?string
    {
        return route('settings.update');
    }

    public function getConfig(): array
    {
        return [
            'api_enabled' => $this->isApiEnabled(),
            'api_get_url' => $this->getGetApiUrl(),
            'api_update_url' => $this->getUpdateApiUrl(),
        ];
    }

}
