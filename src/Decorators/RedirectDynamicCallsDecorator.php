<?php

namespace Settings\Decorators;

use Illuminate\Support\Str;
use Settings\Exceptions\SettingNotRegistered;

/**
 * Validate incoming data
 */
class RedirectDynamicCallsDecorator extends BaseSettingServiceDecorator
{

    public function __call(string $name, array $arguments)
    {
        if(Str::startsWith($name, 'get')) {
            return $this->baseService->getValue($this->getSettingKey($name), empty($arguments) ? null : $arguments[0]);
        }
        if(Str::startsWith($name, 'set') && !empty($arguments)) {
            $this->baseService->setValue($this->getSettingKey($name), $arguments[0], $arguments[1] ?? null);
            return null;
        }

        throw new \BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $name
        ));
    }

    private function getSettingKey(string $methodName): string
    {
        $upperFirstKey = Str::substr($methodName, 3);
        $names = [$upperFirstKey, Str::camel($upperFirstKey), Str::title($upperFirstKey), Str::lower($upperFirstKey), Str::kebab($upperFirstKey), Str::snake($upperFirstKey)];
        foreach($names as $casedName) {
            if($this->baseService->store()->has($casedName)) {
                return $casedName;
            }
        }
        throw new SettingNotRegistered($upperFirstKey);
    }

}
