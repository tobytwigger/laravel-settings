<?php

namespace Settings\Contracts;

use FormSchema\Schema\Field;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

abstract class Setting
{

    private array $appendedGroups = [];

    /**
     * Get the ID of the current model type
     *
     * @return int|null
     */
    abstract public function resolveId(): ?int;

    abstract public function defaultValue(): mixed;

    /**
     * The field schema to show the user when editing the value.
     *
     * @return ?Field
     */
    public function fieldOptions(): ?Field
    {
        return null;
    }

    public function shouldEncrypt(): bool
    {
        if(property_exists($this, 'shouldEncrypt') && is_bool($this->shouldEncrypt)) {
            return $this->shouldEncrypt;
        }
        return config('laravel-settings.encryption.default', true);
    }

    public function key(): string
    {
        return static::class;
    }

    /**
     * A validator to validate any new values.
     *
     * @param mixed $value The new value
     * @return Validator
     */
    public function validator($value): Validator
    {
        return \Illuminate\Support\Facades\Validator::make(
            ['setting' => $value],
            ['setting' => $this->rules()]
        );
    }

    abstract public function rules(): array|string;

    abstract public function type(): string;

    abstract protected function groups(): array;

    /**
     * Get the value of a setting
     *
     * @param int|null $id The ID of the model to query against.
     * @return mixed
     */
    public static function getValue(?int $id = null): mixed
    {
        return \Settings\Setting::getValue(static::class, $id);
    }

    /**
     * Set the default value for a setting
     *
     * @param mixed $value The new value of the setting
     *
     * @throws ValidationException If the validation fails.
     */
    public static function setDefaultValue(mixed $value): void
    {
        \Settings\Setting::setDefaultValue(static::class, $value);
    }

    /**
     * Set the value of a specific setting.
     *
     * @param mixed $value The new value of the setting
     * @param int|null $id The ID of the model to query. Leave blank to resolve.
     */
    public static function setValue(mixed $value, ?int $id = null): void
    {
        \Settings\Setting::setValue(static::class, $value, $id);
    }

    public function appendGroups(array $extraGroups): void
    {
        $this->appendedGroups = $extraGroups;
    }

    public function getGroups(): array
    {
        return array_unique(array_merge(
            $this->groups(),
            $this->appendedGroups
        ));
    }

    public function canWrite(): bool
    {
        return true;
    }

    public function canRead(): bool
    {
        return true;
    }

    public function alias(): ?string
    {
        return null;
    }

}
