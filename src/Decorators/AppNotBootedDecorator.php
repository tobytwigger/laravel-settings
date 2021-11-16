<?php

namespace Settings\Decorators;

use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\AppNotBooted;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Store\Query;

/**
 * Check the settings exists
 */
class AppNotBootedDecorator implements SettingService
{

    public static bool $booted = false;

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
        $this->checkAppBooted();
        return $this->baseService->getValue($key, $id);
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->checkAppBooted();
        $this->baseService->setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->checkAppBooted();
        $this->baseService->setValue($key, $value, $id);
    }

    public function withGroup(string $groupName): Query
    {
        $this->checkAppBooted();
        return $this->baseService->withGroup($groupName);
    }

    public function withAnyGroups(array $groups): Query
    {
        $this->checkAppBooted();
        return $this->baseService->withAnyGroups($groups);
    }

    public function withAllGroups(array $groups): Query
    {
        $this->checkAppBooted();
        return $this->baseService->withAllGroups($groups);
    }

    public function withType(string $type): Query
    {
        $this->checkAppBooted();
        return $this->baseService->withType($type);
    }

    public function withGlobalType(): Query
    {
        $this->checkAppBooted();
        return $this->baseService->withGlobalType();
    }

    public function withUserType(): Query
    {
        $this->checkAppBooted();
        return $this->baseService->withGlobalType();
    }

    public function getSettingByKey(string $key): Setting
    {
        $this->checkAppBooted();
        return $this->baseService->getSettingByKey($key);
    }

    public function search()
    {
        $this->checkAppBooted();
        return $this->baseService->search();
    }

    public function store(): SettingStore
    {
        return $this->baseService->store();
    }

    private function checkAppBooted()
    {
        if(static::$booted === false) {
            throw new AppNotBooted();
        }
    }
}
