<?php

namespace Settings\Tests\Unit\Decorators;

use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Decorators\PermissionDecorator;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Exceptions\SettingUnauthorized;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;

class PermissionDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_proxies_getValue_when_can_read_is_true(){
        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: true, canWrite: false);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getValue('siteName', null)->shouldBeCalled()->willReturn('Site Name 1');
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $this->assertEquals('Site Name 1', $decorator->getValue('siteName'));
    }

    /** @test */
    public function it_throws_an_exception_for_getValue_when_can_read_is_false(){
        $this->expectException(SettingUnauthorized::class);

        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: false, canWrite: true);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getValue('siteName', null)->shouldNotBeCalled();
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->getValue('siteName');
    }

    /** @test */
    public function it_proxies_getSettingByKey_when_can_read_is_true(){
        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: true, canWrite: false);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getSettingByKey('siteName')->shouldBeCalled()->willReturn($setting);
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $this->assertEquals($setting, $decorator->getSettingByKey('siteName'));
    }

    /** @test */
    public function it_throws_an_exception_for_getSettingByKey_when_can_read_is_false(){
        $this->expectException(SettingUnauthorized::class);

        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: false, canWrite: true);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getSettingByKey('siteName')->shouldNotBeCalled();
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->getSettingByKey('siteName');
    }

    /** @test */
    public function it_proxies_setValue_when_can_write_is_true(){
        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: false, canWrite: true);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setValue('siteName', 'New Value', null)->shouldBeCalled();
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->setValue('siteName','New Value');
    }

    /** @test */
    public function it_throws_an_exception_for_setValue_when_can_write_is_false(){
        $this->expectException(SettingUnauthorized::class);

        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: true, canWrite: false);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setValue('siteName','New Value', null)->shouldNotBeCalled();
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->setValue('siteName','New Value');
    }

    /** @test */
    public function it_proxies_setDefaultValue_when_can_write_is_true(){
        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: false, canWrite: true);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setDefaultValue('siteName', 'New Value',)->shouldBeCalled();
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->setDefaultValue('siteName','New Value');
    }

    /** @test */
    public function it_throws_an_exception_for_setDefaultValue_when_can_write_is_false(){
        $this->expectException(SettingUnauthorized::class);

        $setting = $this->createSetting('siteName', 'Site Name 1 - default', 'string', canRead: true, canWrite: false);
        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setDefaultValue('siteName','New Value')->shouldNotBeCalled();
        $decorator = new PermissionDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->setDefaultValue('siteName','New Value');
    }

}

