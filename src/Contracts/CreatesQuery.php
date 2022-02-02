<?php

namespace Settings\Contracts;

use Settings\Store\Query;

interface CreatesQuery
{

    public function withGroup(string $groupName): Query;

    public function withAnyGroup(array $groups): Query;

    public function withAllGroups(array $groups): Query;

    public function withType(string $type): Query;

    public function withGlobalType(): Query;

    public function withUserType(): Query;

    public function search(): Query;
}
