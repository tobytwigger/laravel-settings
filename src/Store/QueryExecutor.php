<?php

namespace Settings\Store;

use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingStore;

class QueryExecutor
{

    private SettingStore $settingStore;

    public function __construct(SettingStore $settingStore)
    {
        $this->settingStore = $settingStore;
    }

    public function search(QueryParameters $parameters): SettingCollection
    {
        return (new SettingCollection($this->settingStore->all()))
            ->filter(fn(Setting $setting) => $this->filterByType($setting, $parameters))
            ->filter(fn(Setting $setting) => $this->filterByAllGroups($setting, $parameters))
            ->filter(fn(Setting $setting) => $this->filterByAnyGroups($setting, $parameters));
    }

    private function filterByType(Setting $setting, QueryParameters $parameters): bool
    {
        return $parameters->getType() === null || $parameters->getType() === $setting->type();
    }

    private function filterByAllGroups(Setting $setting, QueryParameters $parameters): bool
    {
        if(empty($parameters->getGroups())) {
            return true;
        }

        foreach($parameters->getGroups() as $group) {
            if(!in_array($group, $setting->groups())) {
                return false;
            }
        }
        return true;
    }

    private function filterByAnyGroups(Setting $setting, QueryParameters $parameters): bool
    {
        if(empty($parameters->getAnyGroups())) {
            return true;
        }

        foreach($parameters->getAnyGroups() as $group) {
            if(in_array($group, $setting->groups())) {
                return true;
            }
        }
        return false;
    }
}
