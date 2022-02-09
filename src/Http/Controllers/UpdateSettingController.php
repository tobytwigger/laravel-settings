<?php

namespace Settings\Http\Controllers;

use Illuminate\Routing\Controller;
use Settings\Http\Requests\UpdateSettingRequest;
use Settings\Setting;

class UpdateSettingController extends Controller
{

    public function __invoke(UpdateSettingRequest $request)
    {
        $settings = [];

        foreach($request->input('settings', []) as $key => $value) {
            Setting::setValue($key, $value);
            $settings[$key] = $value;
        }

        return $settings;
    }

}
