<?php

namespace Settings\Tests\Integration;

use FormSchema\Schema\Form;
use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSettings;

class SettingQueryTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_filters_by_a_group(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3']);
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3']);

        $this->assertFilters([
            $setting1, $setting2
        ], Query::newQuery()->withGroup('group2')->get());
    }

    /** @test */
    public function it_filters_by_having_many_groups(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3']);
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1']);

        $this->assertFilters([
            $setting1
        ], Query::newQuery()->withGroup('group2')->withGroup('group1')->get());

        $this->assertFilters([
            $setting3
        ], Query::newQuery()->withAllGroups(['group3', 'group1'])->get());
    }

    /** @test */
    public function it_filters_by_having_one_of_many_groups(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3']);
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1']);
        $setting4 = $this->createSetting('key4', 'val1', 'string', true, null, ['group3', 'group4']);

        $this->assertFilters([
            $setting1, $setting2, $setting3
        ], Query::newQuery()->withAnyGroups(['group2', 'group1'])->get());
    }

    /** @test */
    public function it_filters_by_having_a_type(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2'], 'type');
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3'], 'type2');
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1'], 'type2');

        $this->assertFilters([
            $setting2, $setting3
        ], Query::newQuery()->withType('type2')->get());
    }

    /** @test */
    public function it_filters_by_being_global(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2'], GlobalSetting::class);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3'], 'type');
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1'], GlobalSetting::class);

        $this->assertFilters([
            $setting1, $setting3
        ], Query::newQuery()->withType(GlobalSetting::class)->get());

        $this->assertFilters([
            $setting1, $setting3
        ], Query::newQuery()->withGlobalType()->get());
    }

    /** @test */
    public function it_filters_by_being_a_user()
    {
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2'], UserSettings::class);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3'], 'type');
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1'], UserSettings::class);

        $this->assertFilters([
            $setting1, $setting3
        ], Query::newQuery()->withType(UserSettings::class)->get());

        $this->assertFilters([
            $setting1, $setting3
        ], Query::newQuery()->withUserType()->get());
    }

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
        ]), \Settings\Setting::search()->get()->toKeyValuePair());
    }

    /** @test */
    public function toForm_converts_a_search_to_a_form(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val2', 'string', true, 1, ['group1', 'group3']);
        $setting3 = $this->createSetting('key3', 'val3', 'string', true, null, ['group2', 'group3']);

        \Settings\Setting::setValue('key2', 'val2-updated');
        \Settings\Setting::setValue('key3', 'val3-updated');

        $form = Query::newQuery()->get()->toForm();
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

        $form = Query::newQuery()->get()->toForm();
        $this->assertInstanceOf(Form::class, $form);
        $this->assertCount(3, $form->groups());

        $this->assertEquals('Group 1', $form->groups()[0]->getTitle());
        $this->assertEquals('Testing Group 1', $form->groups()[0]->getSubtitle());

        $this->assertEquals('Group 2', $form->groups()[1]->getTitle());
        $this->assertEquals('Testing Group 2', $form->groups()[1]->getSubtitle());

        $this->assertNull($form->groups()[2]->getTitle());
        $this->assertNull($form->groups()[2]->getSubtitle());
    }

    /**
     * @param array $allowed
     * @param \Illuminate\Support\Collection|Setting[] $filtered
     */
    private function assertFilters(array $allowed, \Illuminate\Support\Collection $filtered)
    {
        $this->assertEquals(count($allowed), $filtered->count());

        $allowedKeys = collect($allowed)->map(fn(Setting $setting) => $setting->key());
        foreach($filtered->shift() as $setting) {
            $this->assertContains($setting->key(), $allowedKeys);
        }
    }
}

