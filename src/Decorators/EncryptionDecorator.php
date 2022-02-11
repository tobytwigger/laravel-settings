<?php

namespace Settings\Decorators;

use Settings\Contracts\PersistedSettingRepository;
use Illuminate\Contracts\Encryption\Encrypter;
use Settings\Contracts\Setting;

/**
 * Encrypt data being saved
 */
class EncryptionDecorator implements PersistedSettingRepository
{

    private PersistedSettingRepository $baseService;

    public function __construct(PersistedSettingRepository $baseService)
    {
        $this->baseService = $baseService;
    }

    public function encrypter(): Encrypter {
        return app(Encrypter::class);
    }

    public function getValueWithId(Setting $setting, int $id): mixed
    {
        $value = $this->baseService->getValueWithId($setting, $id);
        if($setting->shouldEncrypt()) {
            return $this->encrypter()->decrypt($value, false);
        }
        return $value;
    }

    public function getDefaultValue(Setting $setting): mixed
    {
        $value = $this->baseService->getDefaultValue($setting);
        if($setting->shouldEncrypt()) {
            return $this->encrypter()->decrypt($value, false);
        }
        return $value;
    }

    public function setDefaultValue(Setting $setting, mixed $value): void
    {
        if($setting->shouldEncrypt()) {
            $value = $this->encrypter()->encrypt($value, false);
        }
        $this->baseService->setDefaultValue($setting, $value);
    }

    public function setValue(Setting $setting, mixed $value, int $id): void
    {
        if($setting->shouldEncrypt()) {
            $value = $this->encrypter()->encrypt($value, false);
        }
        $this->baseService->setValue($setting, $value, $id);
    }
}
