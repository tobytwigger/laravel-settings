<?php

namespace Settings\Tests\Unit\Contracts;

use FormSchema\Schema\Field;
use Illuminate\Support\Arr;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Tests\TestCase;

class SettingTest extends TestCase
{

    /** @test */
    public function shouldEncrypt_returns_the_value_of_the_shouldEncrypt_property_if_set(){
        $setting = new SettingTestDummySetting();

        $setting->shouldEncrypt = true;
        $this->assertTrue($setting->shouldEncrypt());

        $setting->shouldEncrypt = false;
        $this->assertFalse($setting->shouldEncrypt());
    }

    /** @test */
    public function shouldEncrypt_returns_the_config_default_if_the_shouldEncrypt_property_is_null(){
        $setting = new SettingTestDummySetting();

        $setting->shouldEncrypt = null;
        config()->set('laravel-settings.encryption.default', true);
        $this->assertTrue($setting->shouldEncrypt());

        config()->set('laravel-settings.encryption.default', false);
        $this->assertFalse($setting->shouldEncrypt());
    }

    /** @test */
    public function key_returns_the_name_of_the_extending_class(){
        $setting = new SettingTestDummySetting();
        $this->assertEquals(SettingTestDummySetting::class, $setting->key());
    }

    /** @test */
    public function validator_returns_a_validator_with_the_rules_defined(){
        $setting = new SettingTestDummySetting();
        $validator = $setting->validator('test');
        $rules = $validator->getRules();
        $data = $validator->getData();
        $this->assertCount(1, $rules);
        $this->assertCount(1, $data);
        $this->assertEquals(array_key_first($rules), array_key_first($data));
        $this->assertEquals(['string'], Arr::first($rules));
        $this->assertEquals('test', Arr::first($data));
    }

    /** @test */
    public function getValue_calls_the_setting_service_with_a_key(){
        $settingService = $this->prophesize(SettingService::class);
        $settingService->getValue(SettingTestDummySetting::class, 5)->shouldBeCalled()->willReturn('value');
        $this->instance('laravel-settings', $settingService->reveal());

        $this->assertEquals('value', SettingTestDummySetting::getValue(5));
    }

    /** @test */
    public function setDefaultValue_calls_the_setting_service_with_a_key(){
        $settingService = $this->prophesize(SettingService::class);
        $settingService->setDefaultValue(SettingTestDummySetting::class, 'default-value')->shouldBeCalled();
        $this->instance('laravel-settings', $settingService->reveal());

        SettingTestDummySetting::setDefaultValue('default-value');
    }

    /** @test */
    public function setValue_calls_the_setting_service_with_a_key(){
        $settingService = $this->prophesize(SettingService::class);
        $settingService->setValue(SettingTestDummySetting::class, 'new-value', 10)->shouldBeCalled();
        $this->instance('laravel-settings', $settingService->reveal());

        SettingTestDummySetting::setValue('new-value', 10);
    }

    /** @test */
    public function groups_can_be_appended(){
        $setting = new SettingTestDummySetting();

        $this->assertEquals(['some', 'groups'], $setting->getGroups());

        $setting->appendGroups(['appended', 'groups']);

        $this->assertEquals(['some', 'groups', 'appended'], $setting->getGroups());
    }

}

class SettingTestDummySetting extends Setting
{

    public ?bool $shouldEncrypt = true;

    public function resolveId(): ?int
    {
    }

    public function defaultValue(): mixed
    {
    }

    public function fieldOptions(): Field
    {
    }

    public function rules(): array|string
    {
        return 'string';
    }

    public function type(): string
    {
    }

    protected function groups(): array
    {
        return ['some', 'groups'];
    }
}
