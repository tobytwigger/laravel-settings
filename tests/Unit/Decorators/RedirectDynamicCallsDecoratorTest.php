<?php

namespace Settings\Tests\Unit\Decorators;

use Illuminate\Validation\ValidationException;
use Settings\Contracts\SettingStore;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\RedirectDynamicCallsDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Setting;
use Settings\SettingService;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class RedirectDynamicCallsDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function get_forwards_the_call_to_get_value_with_arguments(){
        $setting = $this->createSetting('siteName', 'Site Name 1', ['string']);

        $baseService = $this->getBaseService();
        $baseService->getValue('siteName', 1)->shouldBeCalled()->willReturn('user-value');
        $baseService->getValue('siteName', null)->shouldBeCalled()->willReturn('global-value');

        $decorator = new RedirectDynamicCallsDecorator($baseService->reveal());
        $this->assertEquals('user-value', $decorator->getSiteName(1));
        $this->assertEquals('global-value', $decorator->getSiteName());
    }

    /** @test */
    public function set_forwards_the_call_to_set_value_with_arguments(){
        $setting = $this->createSetting('siteName', 'Site Name 1', ['string']);

        $baseService = $this->getBaseService();
        $baseService->setValue('siteName', 'new-user-value', 1)->shouldBeCalled();
        $baseService->setValue('siteName', 'new-global-value', null)->shouldBeCalled();

        $decorator = new RedirectDynamicCallsDecorator($baseService->reveal());
        $decorator->setSiteName('new-user-value', 1);
        $decorator->setSiteName('new-global-value');
    }

    /** @test */
    public function values_set_through_aliases_still_get_validated(){
        $this->expectException(ValidationException::class);

        $setting = $this->createSetting('\My\Namespace\SiteName', 'Site Name 1', ['string']);
        Setting::alias('siteName', '\My\Namespace\SiteName');

        Setting::setSiteName(['an' => 'error']);
    }


    /** @test */
    public function it_throws_an_exception_if_a_setting_is_not_found_on_get(){
        $this->expectException(SettingNotRegistered::class);

        Setting::getSiteName();
    }

    /** @test */
    public function it_throws_an_exception_if_a_setting_is_not_found_on_set(){
        $this->expectException(SettingNotRegistered::class);

        Setting::setSiteName('value');
    }

    /** @test */
    public function it_throws_a_method_call_exception_if_method_isnt_get_or_set(){
        $this->expectException(\BadMethodCallException::class);

        Setting::testingUnknownFunction();
    }

    /** @test */
    public function it_throws_a_method_call_exception_if_method_is_set_without_a_value(){
        $this->expectException(\BadMethodCallException::class);

        Setting::setSiteName();
    }

    private function getBaseService()
    {
        $service = $this->prophesize(SettingService::class);
        $service->store()->willReturn(app(SettingStore::class));
        return $service;
    }

}

