<?php

namespace Settings\Store;

use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSetting;

class Query
{

    private QueryParameters $parameters;
    private QueryExecutor $executor;

    public function __construct(QueryExecutor $executor)
    {
        $this->parameters = new QueryParameters();
        $this->executor = $executor;
    }

    public static function newQuery(): Query
    {
        return app(static::class);
    }

    public function withGroup(string $groupName): Query
    {
        $this->parameters->withGroups([$groupName]);
        return $this;
    }

    public function withAnyGroup(array $groups): Query
    {
        $this->parameters->withAnyGroup($groups);
        return $this;
    }

    public function withAllGroups(array $groups): Query
    {
        $this->parameters->withGroups($groups);
        return $this;
    }

    public function withType(string $type): Query
    {
        $this->parameters->withType($type);
        return $this;
    }

    public function withGlobalType(): Query
    {
        $this->parameters->withType(GlobalSetting::class);
        return $this;
    }

    public function withUserType(): Query
    {
        $this->parameters->withType(UserSetting::class);
        return $this;
    }

    public function get(): SettingCollection
    {
        return $this->executor->search($this->parameters);
    }

    public function first(): ?Setting
    {
        return $this->get()->first();
    }

}
