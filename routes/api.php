<?php

\Illuminate\Support\Facades\Route::post('setting', \Settings\Http\Controllers\UpdateSettingController::class)
    ->name('settings.update');
