<?php

namespace Settings\Http\Requests;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Rules\SettingKeyIsValidRule;
use Settings\Rules\SettingValueIsValidRule;

class UpdateSettingRequest extends FormRequest
{

    public function authorize(SettingStore $settingStore)
    {
        if(is_array($this->input('settings', []))) {
            foreach($this->input('settings', []) as $key => $value) {
                try {
                    $setting = $settingStore->getByKey($key);
                    if(!$setting->canWrite()) {
                        return Response::deny(sprintf('You do not have permission to update the [%s] setting.', $key));
                    }
                } catch (SettingNotRegistered) {}
            }
        }
        return true;
    }

    public function rules()
    {
        return [
            'settings' => ['required', 'array', 'min:1'],
            'settings.*' => [
                app(SettingKeyIsValidRule::class),
                app(SettingValueIsValidRule::class)
            ]
        ];
    }

}
