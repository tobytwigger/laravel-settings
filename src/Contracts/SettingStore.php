<?php

namespace Settings\Contracts;

use Illuminate\Validation\ValidationException;

interface SettingStore
{

    public function getByKey(string $settingClass): Setting;

    public function register(array $settings, array $extraGroups): void;

    public function registerGroup(string $key, ?string $title = null, ?string $description = null): void;

    public function has(string $settingClass): bool;

}
