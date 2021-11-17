<?php

namespace Settings\Decorators;

use FormSchema\Schema\Field;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Store\Query;

class BaseSettingServiceDecorator implements SettingService
{
    protected SettingService $baseService;

    public function __construct(SettingService $baseService)
    {
        $this->baseService = $baseService;
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
        return $this->baseService->withUserType();
    }

    public function search(): Query
    {
        return $this->baseService->search();
    }

    public function alias(string $alias, string $key): void
    {
        $this->baseService->alias($alias, $key);
    }

    public function store(): SettingStore
    {
        return $this->baseService->store();
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
        $this->baseService->setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->baseService->setValue($key, $value, $id);
    }

    public function getSettingByKey(string $key): Setting
    {
        return $this->baseService->getSettingByKey($key);
    }

    public function create(string $type, string $key, mixed $defaultValue, Field $fieldOptions, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): Setting
    {
        return $this->baseService->create($type,  $key, $defaultValue, $fieldOptions, $groups, $rules, $resolveIdUsing);
    }
}
