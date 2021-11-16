<?php

namespace Settings\Tests\Integration;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
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


}

