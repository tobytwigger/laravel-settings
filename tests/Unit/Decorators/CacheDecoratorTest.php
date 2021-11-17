<?php

namespace Settings\Tests\Unit\Decorators;

use Illuminate\Contracts\Cache\Repository as Cache;
use Prophecy\Argument;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\Setting;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\CacheDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class CacheDecoratorTest extends TestCase
{
    use CreatesSettings;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_remembers_a_call_to_getValueWithId(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getValueWithId($setting, 5)->shouldBeCalledTimes(1)->willReturn('test-value');

        $decorator = new CacheDecorator($baseService->reveal(), cache()->driver());

        for($i = 0; $i < 3; $i++) {
            $this->assertEquals('test-value', $decorator->getValueWithId($setting, 5));
        }
    }

    /** @test */
    public function it_changes_key_on_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getValueWithId($setting, 5)->shouldBeCalledTimes(1)->willReturn('test-value');
        $baseService->getValueWithId($setting, 2)->shouldBeCalledTimes(1)->willReturn('test-value2');

        $decorator = new CacheDecorator($baseService->reveal(), cache()->driver());

        for($i = 0; $i < 3; $i++) {
            $this->assertEquals('test-value', $decorator->getValueWithId($setting, 5));
            $this->assertEquals('test-value2', $decorator->getValueWithId($setting, 2));
        }
    }

    /** @test */
    public function the_ttl_can_be_controlled_through_config(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);

        $cache = $this->prophesize(Cache::class);
        config()->set('laravel-settings.cache.ttl', 55);
        $cache->remember(Argument::any(), 55, Argument::any())->shouldBeCalled()->willReturn('test-value');
        $decorator = new CacheDecorator($baseService->reveal(), $cache->reveal());

        $decorator->getValueWithId($setting, 5);
    }

    /** @test */
    public function it_remembers_a_call_to_getDefaultValue(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getDefaultValue($setting)->shouldBeCalledTimes(1)->willReturn('test-value');

        $decorator = new CacheDecorator($baseService->reveal(), cache()->driver());

        for($i = 0; $i < 3; $i++) {
            $this->assertEquals('test-value', $decorator->getDefaultValue($setting));
        }
    }

    /** @test */
    public function it_sets_the_new_value_in_cache_when_it_sets_a_new_value(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getValueWithId($setting, 5)->shouldNotBeCalled()->willReturn('new-value');
        $baseService->setValue($setting, 'new-value', 5)->shouldBeCalled();

        $decorator = new CacheDecorator($baseService->reveal(), cache()->driver());

        $decorator->setValue($setting, 'new-value', 5);
        $this->assertEquals('new-value', $decorator->getValueWithId($setting, 5));
    }

    /** @test */
    public function it_sets_the_new_default_value_in_cache_when_it_sets_a_new_default_value(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getDefaultValue($setting)->shouldNotBeCalled()->willReturn('new-value');
        $baseService->setDefaultValue($setting, 'new-value')->shouldBeCalled();

        $decorator = new CacheDecorator($baseService->reveal(), cache()->driver());

        $decorator->setDefaultValue($setting, 'new-value');
        $this->assertEquals('new-value', $decorator->getDefaultValue($setting));
    }

}

