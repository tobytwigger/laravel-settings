<?php

namespace Settings\Types;

use Illuminate\Contracts\Validation\Validator;

trait ImplementsSetting
{

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

}
