<?php

namespace Settings\Exceptions;

use Throwable;

class SettingNotRegistered extends \Exception
{

    public function __construct(string $settingClass, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Setting [%s] has not been registered but was referenced', $settingClass),
            500,
            $previous
        );
    }

}
