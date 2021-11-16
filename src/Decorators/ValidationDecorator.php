<?php

namespace Settings\Decorators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Store\Query;

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

    public function registerGroup(string $key, ?string $title = null, ?string $subtitle = null): void
    {
        $this->baseService->registerGroup($key, $title, $subtitle);
    }

    public function getValue(string $key, ?int $id = null): mixed
    {
        return $this->baseService->getValue($key, $id);
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->validateValue($key, $value);
        $this->baseService->setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->validateValue($key, $value);
        $this->baseService->setValue($key, $value, $id);
    }

    private function validateValue(string $key, mixed $value)
    {
        $setting = $this->settingStore->getByKey($key);
        $validator = Validator::make(
            ['setting' => $value],
            ['setting' => $setting->rules()]
        );
        $validator->validate();
    }

    public function withGroup(string $groupName): Query
    {
        return $this->baseService->withGroup($groupName);
    }

    public function withAnyGroups(array $groups): Query
    {
        return $this->baseService->withAnyGroups($groups);
    }

    public function withAllGroups(array $groups): Query
    {
        return $this->baseService->withAllGroups($groups);
    }

    public function withType(string $type): Query
    {
        return $this->baseService->withType($type);
    }

    public function withGlobalType(): Query
    {
        return $this->baseService->withGlobalType();
    }

    public function withUserType(): Query
    {
        return $this->baseService->withGlobalType();
    }

    public function getSettingByKey(string $key): Setting
    {
        return $this->baseService->getSettingByKey($key);
    }

    public function search()
    {
        return $this->baseService->search();
    }

    public function store(): SettingStore
    {
        return $this->baseService->store();
    }
}
