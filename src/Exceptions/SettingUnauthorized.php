<?php

namespace Settings\Exceptions;

use Exception;
use Illuminate\Validation\UnauthorizedException;
use Settings\Contracts\Setting;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SettingUnauthorized extends HttpException
{

    public static function fromSetting(Setting $setting)
    {
        return new static(
            403,
            sprintf('You do not have permission to update the [%s] setting.', $setting->key())
        );
    }
}
