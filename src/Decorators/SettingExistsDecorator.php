<?php

namespace Settings\Decorators;

use Illuminate\Validation\ValidationException;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Store\Query;

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

    public function registerGroup(string $key, ?string $title = null, ?string $subtitle = null): void
    {
        $this->baseService->registerGroup($key, $title, $subtitle);
    }

    public function getValue(string $key, ?int $id = null): mixed
    {
        $this->checkSettingExists($key);
        return $this->baseService->getValue($key, $id);
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->checkSettingExists($key);
        $this->baseService->setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->checkSettingExists($key);
        $this->baseService->setValue($key, $value, $id);
    }

    private function checkSettingExists(string $key): void
    {
        if(!$this->settingStore->has($key)) {
            throw new SettingNotRegistered($key);
        }
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
