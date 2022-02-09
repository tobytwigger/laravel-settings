<?php

namespace Settings\Exceptions;

use Settings\Contracts\Setting;
use Throwable;

class SettingNotRegistered extends \Exception
{

    public static function forSetting(Setting $setting)
    {
        return new static($setting->key());
    }

    public function __construct(string $key, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Setting [%s] has not been registered.', $key),
            500,
            $previous
        );
    }

}
