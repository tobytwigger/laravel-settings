<?php

namespace Settings\Decorators;

use Illuminate\Contracts\Cache\Repository;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\Setting;

/**
 * Cache all results
 */
class CacheDecorator implements PersistedSettingRepository
{

    private PersistedSettingRepository $baseService;

    private Repository $cache;

    public function __construct(PersistedSettingRepository $baseService, Repository $cache)
    {
        $this->baseService = $baseService;
        $this->cache = $cache;
    }

    private function getCacheKey(Setting $setting, ?int $id = null): string
    {
        $key = sprintf('%s:%s', self::class, $setting->key());
        if($id !== null) {
            $key = sprintf('%s,%u', $key, $id);
        }
        return md5($key);
    }

    private function ttl(): ?int
    {
        return config('laravel-settings.cache.ttl', 3600);
    }

    public function getValueWithId(Setting $setting, int $id): mixed
    {
        return $this->cache->remember(
            $this->getCacheKey($setting, $id),
            $this->ttl(),
            fn() => $this->baseService->getValueWithId($setting, $id)
        );
    }

    public function getDefaultValue(Setting $setting): mixed
    {
        return $this->cache->remember(
            $this->getCacheKey($setting),
            $this->ttl(),
            fn() => $this->baseService->getDefaultValue($setting)
        );
    }

    public function setDefaultValue(Setting $setting, mixed $value): void
    {
        $this->baseService->setDefaultValue($setting, $value);
        $this->cache->put(
            $this->getCacheKey($setting),
            $value,
            $this->ttl()
        );
    }

    public function setValue(Setting $setting, mixed $value, int $id): void
    {
        $this->baseService->setValue($setting, $value, $id);
        $this->cache->put(
            $this->getCacheKey($setting, $id),
            $value,
            $this->ttl()
        );
    }
}
