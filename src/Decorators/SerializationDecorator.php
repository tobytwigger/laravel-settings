<?php

namespace Settings\Decorators;

use Settings\Contracts\CastsSettingValue;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\Setting;

/**
 * Cast or serialize data being saved
 */
class SerializationDecorator implements PersistedSettingRepository
{

    private PersistedSettingRepository $baseService;

    public function __construct(PersistedSettingRepository $baseService)
    {
        $this->baseService = $baseService;
    }

    public function getValueWithId(Setting $setting, int $id): mixed
    {
        return $this->unserialize($setting, $this->baseService->getValueWithId($setting, $id));
    }

    public function getDefaultValue(Setting $setting): mixed
    {
        return $this->unserialize($setting, $this->baseService->getDefaultValue($setting));
    }

    public function setDefaultValue(Setting $setting, mixed $value): void
    {
        $this->baseService->setDefaultValue(
            $setting,
            $this->serialize($setting, $value)
        );
    }

    public function setValue(Setting $setting, mixed $value, int $id): void
    {
        $this->baseService->setValue(
            $setting,
            $this->serialize($setting, $value),
            $id
        );
    }

    private function unserialize(Setting $setting, mixed $value): mixed
    {
        $value = unserialize($value);
        return $setting instanceof CastsSettingValue ? $setting->castToValue($value) : $value;
    }

    private function serialize(Setting $setting, mixed $value): string
    {
        return serialize($setting instanceof CastsSettingValue ? $setting->castToString($value) : $value);
    }
}
