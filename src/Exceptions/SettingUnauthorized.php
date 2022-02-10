<?php

namespace Settings\Exceptions;

use Exception;
use Illuminate\Validation\UnauthorizedException;
use Settings\Contracts\Setting;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SettingUnauthorized extends HttpException
{

    public static function fromSetting(Setting $setting, string $action = 'update')
    {
        return new static(
            403,
            sprintf('You do not have permission to %s the [%s] setting.', $action, $setting->key())
        );
    }
}
