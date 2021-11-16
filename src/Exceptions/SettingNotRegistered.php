<?php

namespace Settings\Exceptions;

use Throwable;

class SettingNotRegistered extends \Exception
{

    public function __construct(string $key, Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Setting [%s] has not been registered.', $key),
            500,
            $previous
        );
    }

}
