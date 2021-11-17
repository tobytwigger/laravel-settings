<?php

namespace Settings\Tests\Integration\Anonymous;

use FormSchema\Generator\Field;
use Settings\Anonymous\AnonymousSetting;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class AnonymousSettingTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function an_anonymous_setting_can_be_created()
    {
        $setting = new AnonymousSetting(
            'customType',
            'key1',
            'Default Val',
            fn() => 1,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string',
        );

        $this->assertInstanceOf(AnonymousSetting::class, $setting);
        $this->assertEquals('customType', $setting->type());
        $this->assertEquals('key1', $setting->key());
        $this->assertEquals('Default Val', $setting->defaultValue());
        $this->assertEquals(1, $setting->resolveId());
        $this->assertInstanceOf(\FormSchema\Schema\Field::class, $setting->fieldOptions());
        $this->assertEquals('key1', $setting->fieldOptions()->getId());
        $this->assertEquals('Default Val', $setting->fieldOptions()->getValue());
        $this->assertEquals(['group1', 'group2'], $setting->getGroups());
        $this->assertEquals('string', $setting->rules());
    }

    /** @test */
    public function a_value_can_be_set_and_got_for_an_anonymous_setting()
    {
        $setting = new AnonymousSetting(
            'customType',
            'key1',
            'Default Val',
            fn() => 1,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );
        Setting::register($setting);

        \Settings\Setting::setValue('key1', 'New Value');

        $this->assertEquals('New Value', \Settings\Setting::getValue('key1'));
    }

    /** @test */
    public function a_default_value_can_be_set_and_is_returned(){
        $setting = new AnonymousSetting(
            'customType',
            'key1',
            'Default Val',
            fn() => null,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );
        Setting::register($setting);

        \Settings\Setting::setDefaultValue('key1', 'New Default Value');

        \Settings\Setting::setValue('key1', 'New Custom Value', 1);

        $this->assertEquals('New Default Value', \Settings\Setting::getValue('key1'));
        $this->assertEquals('New Custom Value', \Settings\Setting::getValue('key1', 1));
    }

    /** @test */
    public function the_hardcoded_default_value_is_given_as_default(){

        $setting = new AnonymousSetting(
            'customType',
            'key1',
            'Default Val',
            fn() => 1,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );
        Setting::register($setting);

        $this->assertEquals('Default Val', \Settings\Setting::getValue('key1'));
    }

    /** @test */
    public function the_resolveId_correctly_resolves_an_id(){
        $setting = new AnonymousSetting(
            'customType',
            'key1',
            'Default Val',
            fn() => 1,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $setting2 = new AnonymousSetting(
            'customType2',
            'key1',
            'Default Val',
            fn() => 10,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $setting3 = new AnonymousSetting(
            'customType3',
            'key1',
            'Default Val',
            fn() => null,
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $this->assertEquals(1, $setting->resolveId());
        $this->assertEquals(10, $setting2->resolveId());
        $this->assertEquals(null, $setting3->resolveId());
    }

}

