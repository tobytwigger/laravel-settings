<?php

namespace Settings\Tests\Traits;

use Settings\Contracts\Setting;

trait CreatesSettings
{

    protected function createSetting(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null): Setting
    {
        $setting = $this->makeSetting($key, $defaultValue, $rules, $shouldEncrypt, $resolveId);
        settings()->register($setting);
        return $setting;
    }

    protected function makeSetting(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null)
    {
        return new FakeSetting(
            $key,
            $defaultValue,
            $rules,
            $shouldEncrypt,
            $resolveId
        );
    }

}
