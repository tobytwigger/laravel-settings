<?php

namespace Settings\Contracts;

interface PersistedSettingRepository
{

    public function getValueWithId(Setting $setting, int $id): mixed;

    public function getDefaultValue(Setting $setting): mixed;

    public function setDefaultValue(Setting $setting, mixed $value): void;

    public function setValue(Setting $setting, mixed $value, int $id): void;

}
