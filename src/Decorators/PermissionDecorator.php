<?php

namespace Settings\Decorators;

use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Exceptions\SettingUnauthorized;
use Settings\Store\Query;

/**
 * Check the current user is able to update the permissions
 */
class PermissionDecorator extends BaseSettingServiceDecorator
{

    private SettingStore $settingStore;

    public function __construct(SettingService $baseService, SettingStore $settingStore)
    {
        parent::__construct($baseService);
        $this->settingStore = $settingStore;
    }

    public function getValue(string $key, ?int $id = null): mixed
    {
        $this->checkIsReadable($key);
        return parent::getValue($key, $id);
    }

    public function getSettingByKey(string $key): Setting
    {
        $this->checkIsReadable($key);
        return parent::getSettingByKey($key);
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->checkIsWritable($key);
        parent::setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->checkIsWritable($key);
        parent::setValue($key, $value, $id);
    }

    private function checkIsReadable(string $key): void
    {
        if($this->settingStore->getByKey($key)->canRead() !== true) {
            throw new SettingUnauthorized();
        }
    }

    private function checkIsWritable(string $key): void
    {
        if($this->settingStore->getByKey($key)->canWrite() !== true) {
            throw new SettingUnauthorized();
        }
    }

}
