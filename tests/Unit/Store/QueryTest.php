<?php

namespace Settings\Tests\Unit\Store;

use Settings\Collection\SettingCollection;
use Settings\Setting;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSetting;

class QueryTest extends TestCase
{
    use CreatesSettings;

    public static function newQuery(): Query
    {
        return app(static::class);
    }

    /** @test */
    public function newQuery_returns_a_new_query_instance()
    {
        $this->assertInstanceOf(Query::class, Query::newQuery());
    }

    /** @test */
    public function get_returns_a_collection_instance()
    {
        $this->assertInstanceOf(SettingCollection::class, Query::newQuery()->get());
    }

    /** @test */
    public function no_searches_returns_all_settings()
    {
        $setting1 = $this->createSetting('siteName', 'Site Name 1', 'string');
        $setting2 = $this->createSetting('siteName2', 'Site Name 2', 'string');
        $setting3 = $this->createSetting('siteName3', 'Site Name 3', 'string');

        $collection = Query::newQuery()->get();
        $this->assertInstanceOf(SettingCollection::class, $collection);
        $this->assertCount(3, $collection);
        $this->assertEquals($setting1, $collection->shift());
        $this->assertEquals($setting2, $collection->shift());
        $this->assertEquals($setting3, $collection->shift());
    }

    /** @test */
    public function first_returns_null_if_no_settings_registered()
    {
        $this->assertNull(Query::newQuery()->first());
    }

    /** @test */
    public function first_returns_the_first_setting_if_settings_found()
    {
        $setting1 = $this->createSetting('siteName', 'Site Name 1', 'string');
        $setting2 = $this->createSetting('siteName2', 'Site Name 2', 'string');

        $this->assertEquals($setting1, Query::newQuery()->first());
    }

}
