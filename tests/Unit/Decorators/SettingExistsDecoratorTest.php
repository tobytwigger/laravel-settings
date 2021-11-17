<?php

namespace Settings\Tests\Unit\Decorators;

use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\SettingExistsDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class SettingExistsDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_proxies_getValue_if_the_setting_key_exists(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->getValue('siteName', 1)->shouldBeCalled()->willReturn('test-value');

        $decorator = new SettingExistsDecorator($baseSetting->reveal(), app(SettingStore::class));

        $this->assertEquals('test-value', $decorator->getValue('siteName', 1));

    }

    /** @test */
    public function it_proxies_setValue_if_the_setting_key_exists(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setValue('siteName', 'new-value', 1)->shouldBeCalled();

        $decorator = new SettingExistsDecorator($baseSetting->reveal(), app(SettingStore::class));

        $decorator->setValue('siteName', 'new-value', 1);
    }

    /** @test */
    public function it_proxies_setDefaultValue_if_the_setting_key_exists(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setDefaultValue('siteName', 'new-value')->shouldBeCalled();

        $decorator = new SettingExistsDecorator($baseSetting->reveal(), app(SettingStore::class));

        $decorator->setDefaultValue('siteName', 'new-value');
    }

    /** @test */
    public function getValue_throws_an_exception_if_the_setting_does_not_exist(){
        $this->expectException(SettingNotRegistered::class);

        $baseSetting = $this->prophesize(SettingService::class);
        $decorator = new SettingExistsDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->getValue('siteName', 1);
    }

    /** @test */
    public function setValue_throws_an_exception_if_the_setting_does_not_exist(){
        $this->expectException(SettingNotRegistered::class);

        $baseSetting = $this->prophesize(SettingService::class);
        $decorator = new SettingExistsDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->setValue('siteName', 'new-value', 1);
    }

    /** @test */
    public function setDefaultValue_throws_an_exception_if_the_setting_does_not_exist(){
        $this->expectException(SettingNotRegistered::class);

        $baseSetting = $this->prophesize(SettingService::class);
        $decorator = new SettingExistsDecorator($baseSetting->reveal(), app(SettingStore::class));
        $decorator->setDefaultValue('siteName', 'new-value');
    }

}

