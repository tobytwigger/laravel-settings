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
        return route('settings.get', [], true);
    }

    private function getUpdateApiUrl(): ?string
    {
        return route('settings.update', [], true);
    }

    public function getConfig(): array
    {
        return [
            'api_enabled' => true,
            'api_get_url' => 'http://localhost::4000/api/settings',
            'api_update_url' => 'http://localhost::4000/api/settings',
        ];
    }

}
