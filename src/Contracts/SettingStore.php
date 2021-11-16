<?php

namespace Settings\Contracts;

use Settings\Collection\SettingCollection;

interface SettingStore
{

    public function getByKey(string $key): Setting;

    public function register(array $settings, array $extraGroups): void;

    public function registerGroup(string $key, ?string $title = null, ?string $subtitle = null): void;

    public function has(string $key): bool;

    public function all(): SettingCollection;

    public function groupIsRegistered(string $groupKey): bool;

    public function getGroupTitle(string $groupKey): ?string;

    public function getGroupSubtitle(string $groupKey): ?string;

}
