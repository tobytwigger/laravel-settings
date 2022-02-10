<?php

namespace Settings\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;
use Settings\Contracts\SettingStore;

class ArrayKeyIsValidSettingKeyRule implements Rule
{

    private SettingStore $settingStore;

    public function __construct(SettingStore $settingStore)
    {
        $this->settingStore = $settingStore;
    }

    public function passes($attribute, $value): bool
    {
        if(!$this->settingStore->has(Str::after($attribute, 'settings.'))) {
            return false;
        }
        return true;
    }

    public function message(): string
    {
        return 'The :attribute setting key does not exist.';
    }
}
