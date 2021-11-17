<?php

namespace Settings\Tests\Unit\Collection;

use FormSchema\Schema\Form;
use Settings\Collection\SettingCollection;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class SettingCollectionTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function toKeyValuePair_converts_the_settings_to_key_value_pairs()
    {
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null);
        $setting2 = $this->createSetting('key2', 'val2', 'string', true, 1);
        $setting3 = $this->createSetting('key3', 'val3', 'string', true, null);

        \Settings\Setting::setValue('key2', 'val2-updated');
        \Settings\Setting::setValue('key3', 'val3-updated');

        $this->assertEquals(SettingCollection::make([
            'key1' => 'val1',
            'key2' => 'val2-updated',
            'key3' => 'val3-updated'
        ]), (new SettingCollection([$setting1, $setting2, $setting3]))->toKeyValuePair());
    }

    /** @test */
    public function toKeyValuePair_returns_an_array_if_no_settings_are_registered(){
        $this->assertEquals(SettingCollection::make([]), (new SettingCollection())->toKeyValuePair());
    }

    /** @test */
    public function toForm_converts_a_search_to_a_form(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val2', 'string', true, 1, ['group1', 'group3']);
        $setting3 = $this->createSetting('key3', 'val3', 'string', true, null, ['group2', 'group3']);

        \Settings\Setting::setValue('key2', 'val2-updated');
        \Settings\Setting::setValue('key3', 'val3-updated');

        $form = (new SettingCollection([$setting1, $setting2, $setting3]))->toForm();
        $this->assertInstanceOf(Form::class, $form);
        $this->assertCount(2, $form->groups());

        $this->assertCount(2, $form->groups()[0]->fields());
        $this->assertEquals('key1', $form->groups()[0]->fields()[0]->getId());
        $this->assertEquals('key2', $form->groups()[0]->fields()[1]->getId());

        $this->assertCount(1, $form->groups()[1]->fields());
        $this->assertEquals('key3', $form->groups()[1]->fields()[0]->getId());

    }

    /** @test */
    public function toForm_adds_group_meta_information_if_present(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val2', 'string', true, 1, ['group2', 'group3']);
        $setting3 = $this->createSetting('key3', 'val3', 'string', true, null, ['group3', 'group2']);

        \Settings\Setting::registerGroup('group1', 'Group 1', 'Testing Group 1');
        \Settings\Setting::registerGroup('group2', 'Group 2', 'Testing Group 2');

        $form = (new SettingCollection([$setting1, $setting2, $setting3]))->toForm();
        $this->assertInstanceOf(Form::class, $form);
        $this->assertCount(3, $form->groups());

        $this->assertEquals('Group 1', $form->groups()[0]->getTitle());
        $this->assertEquals('Testing Group 1', $form->groups()[0]->getSubtitle());

        $this->assertEquals('Group 2', $form->groups()[1]->getTitle());
        $this->assertEquals('Testing Group 2', $form->groups()[1]->getSubtitle());

        $this->assertNull($form->groups()[2]->getTitle());
        $this->assertNull($form->groups()[2]->getSubtitle());
    }

    /** @test */
    public function how_the_form_is_created_can_be_controlled(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);

        \Settings\Setting::registerGroup('group1', 'Group 1', 'Testing Group 1');
        \Settings\Setting::registerGroup('group2', 'Group 2', 'Testing Group 2');

        SettingCollection::$convertToFormUsing = fn(SettingCollection $collection) => \FormSchema\Generator\Form::make('success')->form();

        $form = (new SettingCollection([$setting1]))->toForm();
        $this->assertInstanceOf(Form::class, $form);
        $this->assertEquals('success', $form->getTitle());
    }

}
