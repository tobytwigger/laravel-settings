<?php

if(!function_exists('settings')) {
    /**
     * @return \Settings\Contracts\SettingService
     */
    function settings() {
        return app('laravel-settings');
    }
}
