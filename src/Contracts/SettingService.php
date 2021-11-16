<?php

namespace Settings\Contracts;

use Illuminate\Validation\ValidationException;

interface SettingService
{

    /**
     * Register a new setting or settings
     *
     * @param Setting|array $settings The class or an array of settings.
     * @param array $extraGroups Any groups that should be appended to the settings.
     * @return void
     */
    public function register(Setting|array $settings, array $extraGroups = []): void;

    /**
     * Register a new group
     *
     * @param string $key The key of the group.
     * @param string|null $title The title of the group
     * @param string|null $description A description pf the group
     * @return void
     */
    public function registerGroup(string $key, ?string $title = null, ?string $description = null): void;

    /**
     * Get the value of a setting
     *
     * @param string $settingClass The setting class
     * @param int|null $id The ID of the model to query against.
     * @return mixed
     */
    public function getValue(string $settingClass, ?int $id = null): mixed;

    /**
     * Set the default value for a setting
     *
     * @param string $settingClass The setting class
     * @param mixed $value The new value of the setting
     *
     * @throws ValidationException If the validation fails.
     */
    public function setDefaultValue(string $settingClass, mixed $value): void;

    /**
     * Set the value of a specific setting.
     *
     * @param string $settingClass The setting class
     * @param mixed $value The new value of the setting
     * @param int|null $id The ID of the model to query. Leave blank to resolve.
     */
    public function setValue(string $settingClass, mixed $value, ?int $id = null): void;

}
