<?php

namespace Settings\Decorators;

use Illuminate\Support\Facades\Validator;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Store\Query;

/**
 * Validate incoming data
 */
class ValidationDecorator extends BaseSettingServiceDecorator
{

    private SettingStore $settingStore;

    public function __construct(SettingService $baseService, SettingStore $settingStore)
    {
        parent::__construct($baseService);
        $this->settingStore = $settingStore;
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->validateValue($key, $value);
        parent::setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->validateValue($key, $value);
        parent::setValue($key, $value, $id);
    }

    private function validateValue(string $key, mixed $value)
    {
        $setting = $this->settingStore->getByKey($key);
        $validator = $setting->validator($value);
        $validator->validate();
    }

}
