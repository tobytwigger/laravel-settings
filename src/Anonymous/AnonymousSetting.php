<?php

namespace Settings\Anonymous;

use FormSchema\Schema\Field;
use Settings\Contracts\Setting;
use Settings\Types\GlobalSetting;

class AnonymousSetting extends Setting
{

    private ?\Closure $resolveIdUsing = null;

    private string $type;

    private string $key;

    private mixed $defaultValue;

    private ?Field $fieldOptions;

    private string|array $rules;

    /**
     * @var array|string[]
     */
    private array $groups;

    public function __construct(string $type, string $key, mixed $defaultValue, \Closure $resolveIdUsing, ?Field $fieldOptions = null, array $groups = ['default'], array|string $rules = [])
    {
        $this->type = $type;
        $this->key = $key;
        $this->defaultValue = $defaultValue;
        $this->resolveIdUsing = $resolveIdUsing;
        $this->fieldOptions = $fieldOptions;
        $this->rules = $rules;
        $this->groups = $groups;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function resolveId(): ?int
    {
        return call_user_func($this->resolveIdUsing);
    }

    public function defaultValue(): mixed
    {
        return $this->defaultValue;
    }

    public function fieldOptions(): ?Field
    {
        return $this->fieldOptions;
    }

    public function rules(): array|string
    {
        return $this->rules;
    }

    public function type(): string
    {
        return $this->type;
    }

    protected function groups(): array
    {
        return $this->groups;
    }
}
