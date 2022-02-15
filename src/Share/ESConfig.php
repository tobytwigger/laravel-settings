<?php

namespace Settings\Share;

use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;

class ESConfig
{

    private array $config = [];

    public function add(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

    public function addMany(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public static function share(string $key, mixed $value): void
    {
        $instance = app(ESConfig::class);
        $instance->add($key, $value);
    }

}
