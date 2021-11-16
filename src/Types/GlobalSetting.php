<?php

namespace Settings\Types;

use Settings\Contracts\Setting;

abstract class GlobalSetting extends Setting
{
    public function resolveId(): ?int
    {
        return null;
    }

    public function type(): string
    {
        return GlobalSetting::class;
    }
}
