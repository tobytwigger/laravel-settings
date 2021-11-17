<?php

namespace Settings\Tests\Unit\Decorators;

use Illuminate\Validation\ValidationException;
use Settings\Contracts\SettingStore;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\ValidationDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Exceptions\SettingNotRegistered;
use Settings\SettingService;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class ValidationDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_proxies_setValue_if_the_validation_passes(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setValue('siteName', 'new-value', 1)->shouldBeCalled();

        $decorator = new ValidationDecorator($baseSetting->reveal(), app(SettingStore::class));

        $decorator->setValue('siteName', 'new-value', 1);
    }

    /** @test */
    public function it_proxies_setDefaultValue_if_the_validation_passes(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setDefaultValue('siteName', 'new-value')->shouldBeCalled();

        $decorator = new ValidationDecorator($baseSetting->reveal(), app(SettingStore::class));

        $decorator->setDefaultValue('siteName', 'new-value');
    }

    /** @test */
    public function setValue_throws_an_exception_if_the_validation_fails(){
        $this->expectException(ValidationException::class);

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setValue('siteName', ['new-value'], 1)->shouldNotBeCalled();

        $decorator = new ValidationDecorator($baseSetting->reveal(), app(SettingStore::class));

        $decorator->setValue('siteName', ['new-value'], 1);
    }

    /** @test */
    public function setDefaultValue_throws_an_exception_if_the_validation_fails(){
        $this->expectException(ValidationException::class);

        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseSetting = $this->prophesize(SettingService::class);
        $baseSetting->setDefaultValue('siteName', ['new-value'])->shouldNotBeCalled();

        $decorator = new ValidationDecorator($baseSetting->reveal(), app(SettingStore::class));

        $decorator->setDefaultValue('siteName', ['new-value']);
    }

}

