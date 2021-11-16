<?php

namespace Settings\Types;

use Settings\Contracts\Setting;

abstract class GlobalSetting implements Setting
{
    use ImplementsSetting;

    public function resolveId(): ?int
    {
        return null;
    }
}
