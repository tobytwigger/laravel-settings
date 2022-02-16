<?php

namespace Settings\Share;

use Illuminate\Contracts\Config\Repository;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

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
        try {
            return route('settings.get');
        } catch (RouteNotFoundException) {
            return null;
        }
    }

    private function getUpdateApiUrl(): ?string
    {
        try {
            return route('settings.update');
        } catch (RouteNotFoundException) {
            return null;
        }
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
