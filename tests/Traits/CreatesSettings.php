<?php

namespace Settings\Tests\Traits;

use Settings\Contracts\Setting;
use Settings\Types\GlobalSetting;

trait CreatesSettings
{

    protected function createSetting(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null, array $groups = [], string $type = GlobalSetting::class, bool $canRead = true, bool $canWrite = true): Setting
    {
        $setting = $this->makeSetting($key, $defaultValue, $rules, $shouldEncrypt, $resolveId, $groups, $type, $canRead, $canWrite);
        settings()->register($setting);
        return $setting;
    }

    protected function makeSetting(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null, array $groups = [], string $type = GlobalSetting::class, bool $canRead = true, bool $canWrite = true)
    {
        return new FakeSetting(
            $key,
            $defaultValue,
            $rules,
            $shouldEncrypt,
            $resolveId,
            $groups,
            $type,
            $canRead,
            $canWrite
        );
    }

}
