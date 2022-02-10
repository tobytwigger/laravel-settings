<?php

\Illuminate\Support\Facades\Route::post('setting', \Settings\Http\Controllers\UpdateSettingController::class)
    ->name('settings.update');

\Illuminate\Support\Facades\Route::get('setting', \Settings\Http\Controllers\GetSettingController::class)
    ->name('settings.get');
