<?php

namespace Settings\Tests\Integration;

use FormSchema\Generator\Field;
use FormSchema\Schema\Form;
use Settings\Anonymous\AnonymousSetting;
use Settings\Anonymous\AnonymousSettingFactory;
use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSettings;

class AnonymouSettingTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_makes_an_anonymous_setting()
    {
        AnonymousSettingFactory::mapType('customType', fn() => 1);

        $setting = AnonymousSettingFactory::make(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $this->assertCount(0, \Settings\Setting::search()->get());

        $this->assertInstanceOf(AnonymousSetting::class, $setting);
        $this->assertEquals('key1', $setting->key());
        $this->assertEquals('customType', $setting->type());
        $this->assertEquals('string', $setting->rules());
        $this->assertInstanceOf(\FormSchema\Schema\Field::class, $setting->fieldOptions());
        $this->assertEquals('key1', $setting->fieldOptions()->getId());
        $this->assertEquals('Default Val', $setting->fieldOptions()->getValue());
        $this->assertEquals('Default Val', $setting->defaultValue());
        $this->assertEquals(['group1', 'group2'], $setting->getGroups());
        $this->assertEquals(1, $setting->resolveId());
        $this->assertEquals('customType', $setting->type());
    }

    /** @test */
    public function it_registers_an_anonymous_setting()
    {
        AnonymousSettingFactory::mapType('customType', fn() => 1);

        $setting = AnonymousSettingFactory::create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $this->assertCount(1, \Settings\Setting::search()->get());
    }

    /** @test */
    public function a_value_can_be_set_and_got_for_an_anonymous_setting()
    {
        AnonymousSettingFactory::mapType('customType', fn() => null);

        $setting = AnonymousSettingFactory::create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        \Settings\Setting::setValue('key1', 'New Value');

        $this->assertEquals('New Value', \Settings\Setting::getValue('key1'));
    }

    /** @test */
    public function a_default_value_can_be_set_and_is_returned(){
        AnonymousSettingFactory::mapType('customType', fn() => null);

        $setting = AnonymousSettingFactory::create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        \Settings\Setting::setDefaultValue('key1', 'New Default Value');

        \Settings\Setting::setValue('key1', 'New Custom Value', 1);

        $this->assertEquals('New Default Value', \Settings\Setting::getValue('key1'));
        $this->assertEquals('New Custom Value', \Settings\Setting::getValue('key1', 1));
    }

    /** @test */
    public function the_hardcoded_default_value_is_given_as_default(){
        AnonymousSettingFactory::mapType('customType', fn() => null);

        $setting = AnonymousSettingFactory::create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $this->assertEquals('Default Val', \Settings\Setting::getValue('key1'));
    }

    /** @test */
    public function the_resolveId_correctly_resolves_an_id(){
        AnonymousSettingFactory::mapType('customType', fn() => 1);
        AnonymousSettingFactory::mapType('customType2', fn() => 10);
        AnonymousSettingFactory::mapType('customType3', fn() => null);

        $setting = AnonymousSettingFactory::create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $setting2 = AnonymousSettingFactory::create(
            'customType2',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $setting3 = AnonymousSettingFactory::create(
            'customType3',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string'
        );

        $this->assertEquals(1, $setting->resolveId());
        $this->assertEquals(10, $setting2->resolveId());
        $this->assertEquals(null, $setting3->resolveId());
    }

    /** @test */
    public function the_resolveId_callback_can_be_overridden_for_an_anonymous_setting(){
        AnonymousSettingFactory::mapType('customType', fn() => 100);

        $setting = AnonymousSettingFactory::create(
            'customType',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string',
            fn() => 200
        );

        $this->assertEquals(200, $setting->resolveId());
    }

    /** @test */
    public function when_a_resolveId_resolver_is_given_the_rule_type_does_not_matter(){
        $setting = AnonymousSettingFactory::create(
            'Some Random Type',
            'key1',
            'Default Val',
            Field::text('key1')->setValue('Default Val'),
            ['group1', 'group2'],
            'string',
            fn() => 5
        );

        $this->assertEquals('Some Random Type', $setting->type());
        $this->assertEquals(5, $setting->resolveId());
    }
}

