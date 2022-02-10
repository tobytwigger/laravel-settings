<?php

namespace Settings\Http\Requests;

use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Rules\ArrayKeyIsValidSettingKeyRule;
use Settings\Rules\SettingKeyIsValidRule;
use Settings\Rules\SettingValueIsValidRule;

class GetSettingRequest extends FormRequest
{

    public function authorize(SettingStore $settingStore)
    {
        if(is_array($this->query('settings', []))) {
            foreach($this->query('settings', []) as $key) {
                try {
                    $setting = $settingStore->getByKey($key);
                    if(!$setting->canRead()) {
                        return Response::deny(sprintf('You do not have permission to read the [%s] setting.', $key));
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
                app(SettingKeyIsValidRule::class)
            ]
        ];
    }

}
