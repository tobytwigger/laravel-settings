<?php

namespace Settings\Decorators;

use Illuminate\Validation\ValidationException;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;

/**
 * Check the settings exists
 */
class SettingExistsDecorator implements SettingService
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
        $this->checkSettingExists($settingClass);
        return $this->baseService->getValue($settingClass, $id);
    }

    public function setDefaultValue(string $settingClass, mixed $value): void
    {
        $this->checkSettingExists($settingClass);
        $this->baseService->setDefaultValue($settingClass, $value);
    }

    public function setValue(string $settingClass, mixed $value, ?int $id = null): void
    {
        $this->checkSettingExists($settingClass);
        $this->baseService->setValue($settingClass, $value, $id);
    }

    private function checkSettingExists(string $settingClass): void
    {
        if(!$this->settingStore->has($settingClass)) {
            throw new SettingNotRegistered($settingClass);
        }
    }
}
