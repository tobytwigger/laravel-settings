<?php

namespace Settings\Contracts;

use FormSchema\Schema\Field;
use Illuminate\Contracts\Validation\Validator;

interface Setting
{

    /**
     * Get the ID of the current model type
     *
     * @return int|null
     */
    public function resolveId(): ?int;

    public function defaultValue(): mixed;

    public function shouldEncrypt(): bool;

    /**
     * The field schema to show the user when editing the value.
     *
     * @return Field
     */
    public function fieldOptions(): Field;

    /**
     * A validator to validate any new values.
     *
     * @param mixed $value The new value
     * @return Validator
     */
    public function validator($value): Validator;

    public function key(): string;

}
