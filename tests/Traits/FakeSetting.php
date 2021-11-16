<?php

namespace Settings\Tests\Traits;

use FormSchema\Schema\Field;
use Illuminate\Contracts\Validation\Validator;
use Settings\Contracts\Setting;

class FakeSetting implements Setting
{

    private string $key;
    private string|array $rules;
    private bool $shouldEncrypt;
    private mixed $defaultValue;
    private ?int $resolveId;

    public function __construct(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null)
    {
        $this->key = $key;
        $this->rules = $rules;
        $this->shouldEncrypt = $shouldEncrypt;
        $this->defaultValue = $defaultValue;
        $this->resolveId = $resolveId;
    }

    public function resolveId(): ?int
    {
        return $this->resolveId;
    }

    public function defaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function shouldEncrypt(): bool
    {
        return $this->shouldEncrypt;
    }

    public function rules(): array|string
    {
        return $this->rules;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function fieldOptions(): Field
    {
        // TODO: Implement fieldOptions() method.
    }

    public function validator($value): Validator
    {
        return \Illuminate\Support\Facades\Validator::make(
            ['setting' => $value],
            ['setting' => $this->rules()]
        );
    }
}
