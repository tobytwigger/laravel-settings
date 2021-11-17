<?php

namespace Settings\Contracts;

use FormSchema\Schema\Field;
use Illuminate\Validation\ValidationException;

interface SettingService extends CreatesQuery
{

    public function alias(string $alias, string $key): void;

    public function store(): SettingStore;

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
     * @param string|null $subtitle A subtitle for the group
     * @return void
     */
    public function registerGroup(string $key, ?string $title = null, ?string $subtitle = null): void;

    /**
     * Get the value of a setting
     *
     * @param string $key The setting class
     * @param int|null $id The ID of the model to query against.
     * @return mixed
     */
    public function getValue(string $key, ?int $id = null): mixed;

    /**
     * Set the default value for a setting
     *
     * @param string $key The setting class
     * @param mixed $value The new value of the setting
     *
     * @throws ValidationException If the validation fails.
     */
    public function setDefaultValue(string $key, mixed $value): void;

    /**
     * Set the value of a specific setting.
     *
     * @param string $key The setting class
     * @param mixed $value The new value of the setting
     * @param int|null $id The ID of the model to query. Leave blank to resolve.
     */
    public function setValue(string $key, mixed $value, ?int $id = null): void;

    /**
     * Get the setting by its key
     *
     * @param string $key The key of the setting
     * @return Setting
     */
    public function getSettingByKey(string $key): Setting;

    public function create(string $type, string $key, mixed $defaultValue, Field $fieldOptions, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): Setting;

    public function createUser(string $key, mixed $defaultValue, Field $fieldOptions, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): Setting;

    public function createGlobal(string $key, mixed $defaultValue, Field $fieldOptions, array $groups = ['default'], array|string $rules = [], ?\Closure $resolveIdUsing = null): Setting;

}
