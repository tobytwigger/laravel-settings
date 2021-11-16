<?php

namespace Settings\Decorators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;

/**
 * Validate incoming data
 */
class ValidationDecorator implements SettingService
{

    private SettingService $baseService;

    private SettingStore $settingStore;

    public function __construct(SettingService $baseService, SettingStore $settingStore)
    {
        $this->baseService = $baseService;
        $this->settingStore = $settingStore;
    }

    public function register(Setting|array $settings, array $extraGroups = []): void
    {
        $this->baseService->register($settings, $extraGroups);
    }

    public function registerGroup(string $key, ?string $title = null, ?string $description = null): void
    {
        $this->baseService->registerGroup($key, $title, $description);
    }

    public function getValue(string $settingClass, ?int $id = null): mixed
    {
        return $this->baseService->getValue($settingClass, $id);
    }

    public function setDefaultValue(string $settingClass, mixed $value): void
    {
        $this->validateValue($settingClass, $value);
        $this->baseService->setDefaultValue($settingClass, $value);
    }

    public function setValue(string $settingClass, mixed $value, ?int $id = null): void
    {
        $this->validateValue($settingClass, $value);
        $this->baseService->setValue($settingClass, $value, $id);
    }

    private function validateValue(string $settingClass, mixed $value)
    {
        $setting = $this->settingStore->getByKey($settingClass);
        $validator = Validator::make(
            ['setting' => $value],
            ['setting' => $setting->rules()]
        );
        $validator->validate();
    }
}
