<?php

namespace Settings\Decorators;

use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\AppNotBooted;
use Settings\Store\Query;

/**
 * Check the settings exists
 */
class AppNotBootedDecorator extends BaseSettingServiceDecorator
{

    public static bool $booted = false;

    public function getValue(string $key, ?int $id = null): mixed
    {
        $this->checkAppBooted();
        return parent::getValue($key, $id);
    }

    public function setDefaultValue(string $key, mixed $value): void
    {
        $this->checkAppBooted();
        parent::setDefaultValue($key, $value);
    }

    public function setValue(string $key, mixed $value, ?int $id = null): void
    {
        $this->checkAppBooted();
        parent::setValue($key, $value, $id);
    }

    public function withGroup(string $groupName): Query
    {
        $this->checkAppBooted();
        return parent::withGroup($groupName);
    }

    public function withAnyGroup(array $groups): Query
    {
        $this->checkAppBooted();
        return parent::withAnyGroup($groups);
    }

    public function withAllGroups(array $groups): Query
    {
        $this->checkAppBooted();
        return parent::withAllGroups($groups);
    }

    public function withType(string $type): Query
    {
        $this->checkAppBooted();
        return parent::withType($type);
    }

    public function withGlobalType(): Query
    {
        $this->checkAppBooted();
        return parent::withGlobalType();
    }

    public function withUserType(): Query
    {
        $this->checkAppBooted();
        return parent::withUserType();
    }

    public function getSettingByKey(string $key): Setting
    {
        $this->checkAppBooted();
        return parent::getSettingByKey($key);
    }

    public function search(): Query
    {
        $this->checkAppBooted();
        return parent::search();
    }

    private function checkAppBooted()
    {
        if(static::$booted === false) {
            throw new AppNotBooted();
        }
    }

}
