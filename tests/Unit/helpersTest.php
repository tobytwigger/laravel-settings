<?php

namespace Settings\Tests\Unit;

use Settings\Contracts\SettingService;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Spatie\Ray\Settings\Settings;

class helpersTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function settings_returns_an_instance_of_the_translation_manager_when_given_no_parameters()
    {
        $this->assertInstanceOf(SettingService::class, \settings());
    }

    /** @test */
    public function settings_returns_a_value_if_a_key_is_given(){
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, null);

        $this->assertEquals(['default' => 'value'], settings('key1'));
    }

    /** @test */
    public function settings_returns_a_type_value_if_a_key_is_given_and_an_id_is_given(){
        $setting = $this->createSetting('key1', ['default' => 'value'], ['array'], true, 2);

        Setting::setValue('key1', ['My new value'], 2);

        $this->assertEquals(
            ['My new value'],
            settings('key1')
        );
        $this->assertEquals(
            ['My new value'],
            settings('key1', 2)
        );
        $this->assertEquals(
            ['default' => 'value'],
            settings('key1', 1)
        );
    }

}
