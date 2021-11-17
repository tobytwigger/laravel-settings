<?php

if(!function_exists('settings')) {
    /**
     * @return \Settings\Contracts\SettingService
     */
    function settings(?string $key = null, ?int $modelId = null) {
        if($key !== null) {
            return \Settings\Setting::getValue($key, $modelId);
        }
        return app('laravel-settings');
    }
}
