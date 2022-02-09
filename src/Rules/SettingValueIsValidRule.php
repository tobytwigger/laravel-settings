<?php

namespace Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Settings\Contracts\SettingStore;

class SettingValueIsValidRule implements Rule, ValidatorAwareRule
{

    private SettingStore $settingStore;

    private Validator $validator;

    public function __construct(SettingStore $settingStore)
    {
        $this->settingStore = $settingStore;
    }

    public function passes($attribute, $value): bool
    {
        $key = Str::after($attribute, 'settings.');

        if(!$this->settingStore->has($key)) {
            return false;
        }

        $setting = $this->settingStore->getByKey($key);

        $ruleValidator = ValidatorFacade::make([$key => $value], [$key => $setting->rules()]);

        if($ruleValidator->fails()){
            $this->validator->messages()->merge($ruleValidator->errors());
            return true;
        }

        return true;
    }

    public function message(): ?string
    {
        return null;
    }

    public function setValidator($validator): static
    {
        $this->validator = $validator;
        return $this;
    }
}
