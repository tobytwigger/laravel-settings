<?php

namespace Settings\Types;

use Illuminate\Support\Facades\Auth;
use Settings\Contracts\Setting;

abstract class UserSettings extends Setting
{
    public static ?\Closure $resolveUserUsing = null;

    public function resolveId(): ?int
    {
        if(static::$resolveUserUsing !== null) {
            return app()->call(static::$resolveUserUsing);
        }
        return Auth::id();
    }

    public function type(): string
    {
        return UserSettings::class;
    }

}
