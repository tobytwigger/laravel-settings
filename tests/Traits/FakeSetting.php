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
    private bool $canWrite;
    private bool $canRead;
    public ?string $alias = null;
    private ?Field $fieldOptions;

    public function __construct(string $key, mixed $defaultValue, array|string $rules = [], bool $shouldEncrypt = true, ?int $resolveId = null, array $groups = [], string $type = GlobalSetting::class, bool $canRead = true, bool $canWrite = true, ?Field $fieldOptions = null)
    {
        $this->key = $key;
        $this->rules = $rules;
        $this->shouldEncryptValue = $shouldEncrypt;
        $this->defaultValue = $defaultValue;
        $this->resolveId = $resolveId;
        $this->groups = $groups;
        $this->type = $type;
        $this->canWrite = $canWrite;
        $this->canRead = $canRead;
        $this->fieldOptions = $fieldOptions;
    }

    public function alias(): ?string
    {
        return $this->alias;
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

    public function fieldOptions(): ?Field
    {
       return $this->fieldOptions;
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

    public function canWrite(): bool
    {
        return $this->canWrite;
    }

    public function canRead(): bool
    {
        return $this->canRead;
    }


}
