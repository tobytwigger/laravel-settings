<?php

namespace Settings\DatabaseSettings;

use Illuminate\Support\Facades\Crypt;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\Setting;
use Settings\Exceptions\PersistedSettingNotFound;

class DatabaseSettingRepository implements PersistedSettingRepository
{

    public function getValueWithId(Setting $setting, int $id): mixed
    {
        $settingFromDb = SavedSetting::where('key', $setting->key())->where('model_id', $id)->first();

        if($settingFromDb !== null) {
            return $settingFromDb->value;
        }
        throw new PersistedSettingNotFound();
    }

    public function getDefaultValue(Setting $setting): mixed
    {
        $settingFromDb = SavedSetting::where('key', $setting->key())->whereNull('model_id')->first();
        if($settingFromDb !== null) {
            return $settingFromDb->value;
        }
        throw new PersistedSettingNotFound();
    }

    public function setDefaultValue(Setting $setting, mixed $value): void
    {
        SavedSetting::updateOrCreate(
            ['key' => $setting->key(), 'model_id' => null],
            ['value' => $value]
        );
    }

    public function setValue(Setting $setting, mixed $value, int $id): void
    {
        SavedSetting::updateOrCreate(
            ['key' => $setting->key(), 'model_id' => $id],
            ['value' => $value]
        );
    }
}
