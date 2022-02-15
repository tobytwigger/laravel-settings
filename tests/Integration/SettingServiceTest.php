<?php

namespace Settings\Tests\Integration;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Settings\Contracts\CastsSettingValue;
use Settings\Contracts\SettingStore;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Tests\Traits\FakeSetting;
use Settings\Types\GlobalSetting;

class SettingServiceTest extends TestCase
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
    public function settings_can_be_aliased()
    {
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, null);
        Setting::alias('key2', 'key1');

        Setting::setValue('key1', ['My new value']);
        $this->assertEquals(['My new value'], Setting::getValue('key1'));
        $this->assertEquals(['My new value'], Setting::getValue('key2'));
        Setting::setValue('key2', ['My new value 2']);
        $this->assertEquals(['My new value 2'], Setting::getValue('key1'));
        $this->assertEquals(['My new value 2'], Setting::getValue('key2'));
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
    public function it_registers_an_alias_if_the_setting_has_one(){
        $setting1 = $this->createSetting('setting1', 'value1');
        $setting2 = $this->createSetting('setting2', 'value2');
        $setting1->alias = 'alias1';
        $setting2->alias = null;

        Setting::register($setting1);
        Setting::register($setting2);

        /** @var SettingStore $store */
        $store = app(SettingStore::class);

        $this->assertTrue($store->has('setting1'));
        $this->assertTrue($store->has('setting2'));
        $this->assertTrue($store->has('alias1'));
    }

}

