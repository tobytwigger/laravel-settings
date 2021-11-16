<?php

namespace Settings\Decorators;

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
        return unserialize(
            $this->baseService->getValueWithId($setting, $id)
        );
    }

    public function getDefaultValue(Setting $setting): mixed
    {
        return unserialize(
            $this->baseService->getDefaultValue($setting)
        );
    }

    public function setDefaultValue(Setting $setting, mixed $value): void
    {
        $this->baseService->setDefaultValue($setting, serialize($value));
    }

    public function setValue(Setting $setting, mixed $value, int $id): void
    {
        $this->baseService->setValue($setting, serialize($value), $id);
    }
}
