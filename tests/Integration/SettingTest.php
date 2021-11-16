<?php

namespace Settings\Tests\Integration;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Settings\Contracts\CastsSettingValue;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Tests\Traits\FakeSetting;
use Settings\Types\GlobalSetting;

class SettingTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_gets_the_default_value_of_the_dummy_global_setting()
    {
        $this->createSetting('key1', ['default' => 'value'], ['array'], true, null);
        $this->assertEquals(
            ['default' => 'value'],
            Setting::getValue('key1')
        );
    }

    /** @test */
    public function the_default_value_can_be_overridden_for_the_dummy_global_setting()
    {
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, null);

        Setting::setDefaultValue('key1', ['My new value']);

        $this->assertEquals(
            ['My new value'],
            Setting::getValue('key1')
        );
    }

    /** @test */
    public function the_default_value_can_be_overridden_using_set_value_for_the_dummy_global_setting()
    {
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, null);

        Setting::setValue('key1', ['My new value']);

        $this->assertEquals(
            ['My new value'],
            Setting::getValue('key1')
        );
    }

    /** @test */
    public function a_value_can_be_set_for_a_user_for_a_global_setting()
    {
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, null);

        Setting::setValue('key1', ['My new value'], 2);

        $this->assertEquals(
            ['My new value'],
            Setting::getValue('key1', 2)
        );
        $this->assertEquals(
            ['default' => 'value'],
            Setting::getValue('key1', 1)
        );
        $this->assertEquals(
            ['default' => 'value'],
            Setting::getValue('key1')
        );
    }

    /** @test */
    public function a_value_can_be_set_for_a_user_for_a_model_setting()
    {
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, 2);

        Setting::setValue('key1', ['My new value']);

        $this->assertEquals(
            ['My new value'],
            Setting::getValue('key1')
        );
        $this->assertEquals(
            ['default' => 'value'],
            Setting::getValue('key1', 1)
        );
    }

    /** @test */
    public function it_throws_an_exception_if_the_setting_does_not_exist(){
        $this->expectException(SettingNotRegistered::class);
        $this->expectExceptionMessage('Setting [wrong-key] has not been registered.');

        Setting::getValue('wrong-key');
    }

    /** @test */
    public function it_gets_the_default_value_of_the_dummy_global_setting_through_the_setting()
    {
        $setting = $this->createSetting(FakeSetting::class, ['default' => 'value'], ['array'], true, null);

        $this->assertEquals(
            ['default' => 'value'],
            FakeSetting::getValue()
        );
    }

    /** @test */
    public function complex_objects_can_be_saved(){
        $settingObject = $this->createSetting('key-object', ['default' => 'value'], [], true, null);

        $class = new \stdClass();
        $class->test = 1;
        $class->another = ['testing', '123'];

        Setting::setValue('key-object', $class);

        $this->assertEquals(
            1,
            Setting::getValue('key-object')->test
        );
        $this->assertEquals(
            ['testing', '123'],
            Setting::getValue('key-object')->another
        );
    }

    /** @test */
    public function settings_can_control_serialization(){
        $object = new class {
            public $test = 1;
            public $otherTest = ['testing', '123'];
        };
        $setting = new DummySettingTestWithCasting('key-object', null);
        Setting::register($setting);

        Setting::setValue('key-object', $object);


        $this->assertEquals(
            1,
            Setting::getValue('key-object')->test
        );
        $this->assertEquals(
            ['testing', '123'],
            Setting::getValue('key-object')->otherTest
        );
    }

}


class DummySettingTestWithCasting extends FakeSetting implements CastsSettingValue
{

    public function castToString($value): string
    {
        return json_encode([
            'test' => $value->test,
            'other' => $value->otherTest
        ]);
    }

    public function castToValue(string $value): mixed
    {
        $value = json_decode($value, true);
        $class = new class {
            public $test;
            public $otherTest;
        };
        $class->test = $value['test'];
        $class->otherTest = $value['other'];
        return $class;
    }


}
