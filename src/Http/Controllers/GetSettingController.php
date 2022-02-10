<?php

namespace Settings\Http\Controllers;

use Illuminate\Routing\Controller;
use Settings\Http\Requests\GetSettingRequest;
use Settings\Setting;

class GetSettingController extends Controller
{

    public function __invoke(GetSettingRequest $request)
    {
        $settings = [];

        foreach($request->query('settings', []) as $key) {
            $settings[$key] = Setting::getValue($key);
        }

        return $settings;
    }

}
