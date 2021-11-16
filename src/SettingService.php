<?php

namespace Settings;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingStore;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\SettingService as SettingServiceContract;
use Settings\Exceptions\PersistedSettingNotFound;
use Settings\Store\Query;

class SettingService implements SettingServiceContract
{

    private PersistedSettingRepository $persistedSettings;
    private SettingStore $settingStore;

    public function __construct(PersistedSettingRepository $persistedSettings, SettingStore $settingStore)
    {
        $this->persistedSettings = $persistedSettings;
        $this->settingStore = $settingStore;
    }

    public function register(Setting|array $settings, array $extraGroups = []): void
    {
        $this->settingStore->register(Arr::wrap($settings), $extraGroups);
    }

    public function registerGroup(string $key, ?string $title = null, ?string $subtitle = null): void
    {
        $this->settingStore->registerGroup($key, $title, $subtitle);
    }

    public function getValue(string $key, ?int $id = null): mixed
    {
        $setting = $this->settingStore->getByKey($key);

        // If the ID is null, try and resolve it from the setting type.
        if($id === null) {
            $id = $setting->resolveId();
        }

        // If ID is now not null, get the setting value for the ID. Pass over if not found
        if($id !== null) {
            try {
                return $this->persistedSettings->getValueWithId($setting, $id);
            } catch (PersistedSettingNotFound $e) {}
        }

        // Try and get a default value, if set
        try {
            return $this->persistedSettings->getDefaultValue($setting);
        } catch (PersistedSettingNotFound $e) {}

        // Get the hard-coded default value
        return $setting->defaultValue();
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $setting = $this->settingStore->getByKey($key);

        $this->persistedSettings->setDefaultValue($setting, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $setting = $this->settingStore->getByKey($key);
        // If the ID is null, try and resolve it from the setting type.
        if($id === null) {
            $id = $setting->resolveId();
        }

        if($id !== null) {
            $this->persistedSettings->setValue($setting, $value, $id);
        } else {
            $this->persistedSettings->setDefaultValue($setting, $value);
        }
    }

    public function search(): Query
    {
        return Query::newQuery();
    }

    public function withGroup(string $groupName): Query
    {
        return Query::newQuery()->withGroup($groupName);
    }

    public function withAnyGroups(array $groups): Query
    {
        return Query::newQuery()->withAnyGroups($groups);
    }

    public function withAllGroups(array $groups): Query
    {
        return Query::newQuery()->withAllGroups($groups);
    }

    public function withType(string $type): Query
    {
        return Query::newQuery()->withType($type);
    }

    public function withGlobalType(): Query
    {
        return Query::newQuery()->withGlobalType();
    }

    public function withUserType(): Query
    {
        return Query::newQuery()->withUserType();
    }

    public function getSettingByKey(string $key): Setting
    {
        return $this->settingStore->getByKey($key);
    }

    public function store(): SettingStore
    {
        return $this->settingStore;
    }
}
