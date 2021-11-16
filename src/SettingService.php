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

    public function registerGroup(string $key, ?string $title = null, ?string $description = null): void
    {
        $this->settingStore->registerGroup($key, $title, $description);
    }

    public function getValue(string $settingClass, ?int $id = null): mixed
    {
        $setting = $this->settingStore->getByKey($settingClass);

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

    public function setDefaultValue(string $settingClass, mixed $value): void
    {
        $setting = $this->settingStore->getByKey($settingClass);

        $this->persistedSettings->setDefaultValue($setting, $value);
    }

    public function setValue(string $settingClass, mixed $value, ?int $id = null): void
    {
        $setting = $this->settingStore->getByKey($settingClass);
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
}
