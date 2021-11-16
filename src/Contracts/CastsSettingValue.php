<?php

namespace Settings\Contracts;

use Settings\Store\Query;

interface CastsSettingValue
{

    public function castToString($value): string;

    public function castToValue(string $value): mixed;

}
