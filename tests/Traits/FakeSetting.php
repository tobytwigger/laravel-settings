<?php

namespace Settings\Tests\Traits;

use FormSchema\Schema\Field;
use Illuminate\Contracts\Validation\Validator;
use Settings\Contracts\Setting;
use Settings\Types\GlobalSetting;

class FakeSetting extends Setting
{

    private string $key;
    private string|array $rules;
    protected bool $shouldEncryptValue;
    private mixed $defaultValue;
    private ?int $resolveId;
    private array $groups;
    private string $type;

    public function __construct(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null, array $groups = [], string $type = GlobalSetting::class)
    {
        $this->key = $key;
        $this->rules = $rules;
        $this->shouldEncryptValue = $shouldEncrypt;
        $this->defaultValue = $defaultValue;
        $this->resolveId = $resolveId;
        $this->groups = $groups;
        $this->type = $type;
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
        return $this->shouldEncryptValue;
    }

    public function rules(): array|string
    {
        return $this->rules;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function groups(): array
    {
        return $this->groups;
    }

    public function fieldOptions(): Field
    {
       return \FormSchema\Generator\Field::textInput($this->key())
           ->setValue($this->defaultValue());
    }

    public function validator($value): Validator
    {
        return \Illuminate\Support\Facades\Validator::make(
            ['setting' => $value],
            ['setting' => $this->rules()]
        );
    }

    public function type(): string
    {
        return $this->type;
    }

}
