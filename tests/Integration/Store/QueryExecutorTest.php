<?php

namespace Settings\Tests\Integration\Store;

use Settings\Contracts\Setting;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSetting;

class QueryExecutorTest extends TestCase
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
    public function it_includes_extra_groups_with_filtering(){
        $setting1 = $this->makeSetting('key1', 'val1', 'string', true, null, ['group1', 'group3']);
        $setting2 = $this->makeSetting('key2', 'val1', 'string', true, null, []);
        $setting3 = $this->makeSetting('key3', 'val1', 'string', true, null, ['group3', 'group1']);

        \Settings\Setting::register([$setting1, $setting2], ['group1', 'group2']);
        \Settings\Setting::register($setting3);

        $this->assertFilters([
            $setting1, $setting2
        ], Query::newQuery()->withGroup('group2')->withGroup('group1')->get());
    }

    /** @test */
    public function it_filters_by_having_one_of_many_groups(){
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2']);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3']);
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1']);
        $setting4 = $this->createSetting('key4', 'val1', 'string', true, null, ['group3', 'group4']);

        $this->assertFilters([
            $setting1, $setting2, $setting3
        ], Query::newQuery()->withAnyGroup(['group2', 'group1'])->get());
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
        $setting1 = $this->createSetting('key1', 'val1', 'string', true, null, ['group1', 'group2'], UserSetting::class);
        $setting2 = $this->createSetting('key2', 'val1', 'string', true, null, ['group2', 'group3'], 'type');
        $setting3 = $this->createSetting('key3', 'val1', 'string', true, null, ['group3', 'group1'], UserSetting::class);

        $this->assertFilters([
            $setting1, $setting3
        ], Query::newQuery()->withType(UserSetting::class)->get());

        $this->assertFilters([
            $setting1, $setting3
        ], Query::newQuery()->withUserType()->get());
    }

    /**
     * @param array $allowed
     * @param \Illuminate\Support\Collection|Setting[] $filtered
     */
    private function assertFilters(array $allowed, \Illuminate\Support\Collection $filtered)
    {
        $this->assertEquals(count($allowed), $filtered->count());

        $allowedKeys = collect($allowed)->map(fn(Setting $setting) => $setting->key());
        foreach($filtered as $setting) {
            $this->assertContains($setting->key(), $allowedKeys);
        }
    }
}

