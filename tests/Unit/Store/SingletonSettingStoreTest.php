<?php

namespace Settings\Tests\Unit\Store;

use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Store\Query;
use Settings\Store\SingletonSettingStore;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class SingletonSettingStoreTest extends TestCase
{
    use CreatesSettings;

    private SingletonSettingStore $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->store = new SingletonSettingStore();
    }

    /** @test */
    public function getByKey_gets_a_setting_by_key(){
        $setting = $this->makeSetting('siteName', 'Site Name 1', 'string');
        $this->store->register([$setting], []);

        $this->assertEquals($setting, $this->store->getByKey('siteName'));
    }

    /** @test */
    public function getByKey_throws_an_exception_if_a_setting_not_found(){
        $this->expectException(SettingNotRegistered::class);

        $this->store->getByKey('siteName');
    }

    /** @test */
    public function getByKey_accepts_an_alias(){
        $setting = $this->makeSetting('siteName', 'Site Name 1', 'string');
        $this->store->register([$setting], []);
        $this->store->alias('my site name', 'siteName');

        $this->assertEquals($setting, $this->store->getByKey('siteName'));
        $this->assertEquals($setting, $this->store->getByKey('my site name'));
    }

    /** @test */
    public function has_checks_if_a_setting_exists(){
        $setting = $this->makeSetting('siteName', 'Site Name 1', 'string');
        $this->store->register([$setting], []);

        $this->assertTrue($this->store->has('siteName'));
        $this->assertFalse($this->store->has('invalid'));
    }

    /** @test */
    public function has_takes_aliases_into_account(){
        $setting = $this->makeSetting('siteName', 'Site Name 1', 'string');
        $this->store->alias('my site name', 'siteName');
        $this->store->register([$setting], []);

        $this->assertTrue($this->store->has('siteName'));
        $this->assertTrue($this->store->has('my site name'));
        $this->assertFalse($this->store->has('invalid'));
    }

    /** @test */
    public function there_can_be_many_aliases(){
        $setting = $this->makeSetting('siteName', 'Site Name 1', 'string');
        $this->store->alias('my site name', 'siteName');
        $this->store->alias('my site name two', 'siteName');
        $this->store->register([$setting], []);

        $this->assertTrue($this->store->has('siteName'));
        $this->assertTrue($this->store->has('my site name'));
        $this->assertTrue($this->store->has('my site name two'));

        $this->assertEquals($setting, $this->store->getByKey('siteName'));
        $this->assertEquals($setting, $this->store->getByKey('my site name'));
        $this->assertEquals($setting, $this->store->getByKey('my site name two'));
    }

    /** @test */
    public function all_returns_all_settings()
    {
        $setting1 = $this->makeSetting('siteName', 'Site Name 1', 'string');
        $setting2 = $this->makeSetting('siteName2', 'Site Name 2', 'string');
        $setting3 = $this->makeSetting('siteName3', 'Site Name 3', 'string');
        $this->store->register([$setting1, $setting2, $setting3], []);

        $collection = $this->store->all();
        $this->assertInstanceOf(SettingCollection::class, $collection);
        $this->assertCount(3, $collection);

        $this->assertEquals($setting1, $collection->shift());
        $this->assertEquals($setting2, $collection->shift());
        $this->assertEquals($setting3, $collection->shift());

    }

    /** @test */
    public function groupIsRegistered_returns_if_a_group_is_registered(){
        $this->store->registerGroup('group1', 'Group 1', 'Grp 1 Subtitle');

        $this->assertTrue($this->store->groupIsRegistered('group1'));
        $this->assertFalse($this->store->groupIsRegistered('group2'));
    }

    /** @test */
    public function getGroupTitle_returns_the_registered_group_title(){
        $this->store->registerGroup('group1', 'Group 1', 'Grp 1 Subtitle');

        $this->assertEquals('Group 1', $this->store->getGroupTitle('group1'));
    }

    /** @test */
    public function getGroupTitle_returns_null_if_no_title_registered(){
        $this->assertNull($this->store->getGroupTitle('group1'));
    }

    /** @test */
    public function getGroupSubtitle_returns_the_registered_group_subtitle(){
        $this->store->registerGroup('group1', 'Group 1', 'Grp 1 Subtitle');

        $this->assertEquals('Grp 1 Subtitle', $this->store->getGroupSubtitle('group1'));
    }

    /** @test */
    public function getGroupSubtitle_returns_null_if_no_subtitle_registered(){
        $this->assertNull($this->store->getGroupSubtitle('group1'));

    }


}
