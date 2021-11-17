<?php

namespace Settings\Tests\Unit;

use Prophecy\Argument;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\SettingStore;
use Settings\SettingService;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class SettingServiceTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function register_registers_a_setting(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $store = $this->prophesize(SettingStore::class);
        $store->register([$setting], ['group1'])->shouldBeCalled();
        $this->instance(SettingStore::class, $store->reveal());

        $service = new SettingService(app(PersistedSettingRepository::class), $store->reveal());
        $service->register($setting, ['group1']);
    }

    /** @test */
    public function registerGroup_registers_a_group(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $store = $this->prophesize(SettingStore::class);
        $store->registerGroup('group1', 'Group 1', 'Grp 1 Subtitle')->shouldBeCalled();
        $this->instance(SettingStore::class, $store->reveal());

        $service = new SettingService(app(PersistedSettingRepository::class), $store->reveal());
        $service->registerGroup('group1', 'Group 1', 'Grp 1 Subtitle');
    }

    /** @test */
    public function alias_registers_an_alias(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $store = $this->prophesize(SettingStore::class);
        $store->alias('alias', 'key')->shouldBeCalled();
        $this->instance(SettingStore::class, $store->reveal());

        $service = new SettingService(app(PersistedSettingRepository::class), $store->reveal());
        $service->alias('alias', 'key');
    }


    /** @test */
    public function search_returns_a_new_query(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $service = new SettingService(app(PersistedSettingRepository::class), app(SettingStore::class));
        $this->assertInstanceOf(Query::class, $service->search());
    }

    /** @test */
    public function store_returns_the_store_instance(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $store = $this->prophesize(SettingStore::class);

        $service = new SettingService(app(PersistedSettingRepository::class), $store->reveal());
        $this->assertEquals($store->reveal(), $service->store());
    }

    /** @test */
    public function getSettingByKey_returns_a_setting_class_by_key(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $service = new SettingService(app(PersistedSettingRepository::class), app(SettingStore::class));
        $this->assertEquals($setting, $service->getSettingByKey('siteName'));
    }

    /** @test */
    public function with_functions_returns_a_new_query(){
        $setting = new SettingService($this->prophesize(PersistedSettingRepository::class)->reveal(), $this->prophesize(SettingStore::class)->reveal());
        $this->assertInstanceOf(Query::class, $setting->withGroup('GroupName'));
        $this->assertInstanceOf(Query::class, $setting->withAnyGroups(['GroupName']));
        $this->assertInstanceOf(Query::class, $setting->withAllGroups(['GroupName']));
        $this->assertInstanceOf(Query::class, $setting->withType('GroupName'));
        $this->assertInstanceOf(Query::class, $setting->withGlobalType());
        $this->assertInstanceOf(Query::class, $setting->withUserType());
    }

}
