<?php

namespace Settings\Tests\Unit;

use FormSchema\Generator\Field;
use Prophecy\Argument;
use Settings\Anonymous\AnonymousSetting;
use Settings\Anonymous\AnonymousSettingFactory;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\SettingStore;
use Settings\Setting;
use Settings\SettingService;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSetting;

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
    public function create_returns_a_new_registered_setting(){
        $service = new SettingService(app(PersistedSettingRepository::class), app(SettingStore::class));

        $setting = $service->create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string',
            fn() => 1,
        );

        $this->assertCount(1, \Settings\Setting::search()->get());

        $this->assertInstanceOf(AnonymousSetting::class, $setting);
        $this->assertEquals('key1', $setting->key());
        $this->assertEquals('customType', $setting->type());
        $this->assertEquals('string', $setting->rules());
        $this->assertInstanceOf(\FormSchema\Schema\Field::class, $setting->fieldOptions());
        $this->assertEquals('key1', $setting->fieldOptions()->getId());
        $this->assertEquals('Default Val', $setting->fieldOptions()->getValue());
        $this->assertEquals('Default Val', $setting->defaultValue());
        $this->assertEquals(['group1', 'group2'], $setting->getGroups());
        $this->assertEquals(1, $setting->resolveId());
        $this->assertEquals('customType', $setting->type());
    }

    /** @test */
    public function createUser_returns_a_new_registered_user_setting(){
        $service = new SettingService(app(PersistedSettingRepository::class), app(SettingStore::class));

        $guard = $this->prophesize(\Illuminate\Contracts\Auth\Guard::class);
        $guard->id()->shouldBeCalled()->willReturn(4);
        $this->instance('auth', $guard->reveal());

        $setting = $service->createUser(
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val')
        );

        $this->assertCount(1, \Settings\Setting::search()->get());

        $this->assertInstanceOf(AnonymousSetting::class, $setting);
        $this->assertEquals('user', $setting->type());
        $this->assertEquals(4, $setting->resolveId());
    }

    /** @test */
    public function createGlobal_returns_a_new_registered_global_setting(){
        $service = new SettingService(app(PersistedSettingRepository::class), app(SettingStore::class));

        $setting = $service->createGlobal(
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val')
        );

        $this->assertCount(1, \Settings\Setting::search()->get());

        $this->assertInstanceOf(AnonymousSetting::class, $setting);
        $this->assertEquals('global', $setting->type());
        $this->assertNull($setting->resolveId());
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
