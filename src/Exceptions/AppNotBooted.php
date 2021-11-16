<?php

namespace Settings\Exceptions;

use Throwable;

class AppNotBooted extends \Exception
{

    public function __construct(Throwable $previous = null)
    {
        parent::__construct(
            'The app has not been booted.',
            500,
            $previous
        );
    }

}
