<?php

namespace Settings\Decorators;

use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Store\Query;

/**
 * Check the settings exists
 */
class SettingExistsDecorator extends BaseSettingServiceDecorator
{

    private SettingStore $settingStore;

    public function __construct(SettingService $baseService, SettingStore $settingStore)
    {
        parent::__construct($baseService);
        $this->settingStore = $settingStore;
    }

    public function getValue(string $key, ?int $id = null): mixed
    {
        $this->checkSettingExists($key);
        return parent::getValue($key, $id);
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->checkSettingExists($key);
        parent::setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->checkSettingExists($key);
        parent::setValue($key, $value, $id);
    }

    private function checkSettingExists(string $key): void
    {
        if(!$this->settingStore->has($key)) {
            throw new SettingNotRegistered($key);
        }
    }

}
