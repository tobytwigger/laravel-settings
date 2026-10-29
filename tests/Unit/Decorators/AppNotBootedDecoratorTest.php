<?php

namespace Settings\Tests\Unit\Decorators;

use Settings\Decorators\AppNotBootedDecorator;
use PHPUnit\Framework\Attributes\Test;
use Settings\Exceptions\AppNotBooted;
use Settings\SettingService;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class AppNotBootedDecoratorTest extends TestCase
{
    use CreatesSettings;

    #[Test]
    public function it_throws_an_exception_if_the_app_is_not_booted()
    {
        $this->expectException(AppNotBooted::class);
        AppNotBootedDecorator::$booted = false;

        \Settings\Setting::withType('type')->get();
    }

    #[Test]
    public function it_does_not_throw_an_exception_if_the_app_is_booted()
    {
        AppNotBootedDecorator::$booted = true;

        $val = \Settings\Setting::withType('type')->get();

        $this->assertCount(0, $val);
    }

    #[Test]
    public function it_proxies_getValue_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getValue('siteName', 1)->shouldBeCalled()->willReturn('test-value');

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertEquals('test-value', $decorator->getValue('siteName', 1));
    }

    #[Test]
    public function it_proxies_setDefaultValue_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setDefaultValue('siteName', 'new-value')->shouldBeCalled();

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $decorator->setDefaultValue('siteName', 'new-value');
    }

    #[Test]
    public function it_proxies_setValue_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setValue('siteName', 'new-value', 1)->shouldBeCalled();

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $decorator->setValue('siteName', 'new-value', 1);
    }

    #[Test]
    public function it_proxies_withGroup_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->withGroup('groupName')->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->withGroup('groupName'));
    }

    #[Test]
    public function it_proxies_withAnyGroups_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->withAnyGroup(['groupName'])->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->withAnyGroup(['groupName']));
    }

    #[Test]
    public function it_proxies_withAllGroups_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->withAllGroups(['groupName'])->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->withAllGroups(['groupName']));
    }

    #[Test]
    public function it_proxies_withType_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->withType('type')->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->withType('type'));
    }

    #[Test]
    public function it_proxies_withGlobalType_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->withGlobalType()->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->withGlobalType());
    }

    #[Test]
    public function it_proxies_withUserType_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->withUserType()->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->withUserType());
    }

    #[Test]
    public function it_proxies_getSettingByKey_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getSettingByKey('siteName')->shouldBeCalled()->willReturn($setting);

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertEquals($setting, $decorator->getSettingByKey('siteName'));
    }

    #[Test]
    public function it_proxies_search_if_app_is_booted(){
        AppNotBootedDecorator::$booted = true;

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->search()->shouldBeCalled()->willReturn(Query::newQuery());

        $decorator = new AppNotBootedDecorator($baseSetting->reveal());

        $this->assertInstanceOf(Query::class, $decorator->search());
    }
}

