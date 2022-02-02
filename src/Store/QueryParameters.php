<?php

namespace Settings\Store;

class QueryParameters
{

    private array $allGroups = [];

    private ?string $type = null;

    private array $anyGroups = [];

    public function withGroups(array $groups)
    {
        $this->allGroups = array_merge($this->allGroups, $groups);
    }

    public function withType(string $type)
    {
        $this->type = $type;
    }

    public function withAnyGroup(array $groups)
    {
        $this->anyGroups = $groups;
    }

    public function getGroups(): array
    {
        return $this->allGroups;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getAnyGroups(): array
    {
        return $this->anyGroups;
    }
}
