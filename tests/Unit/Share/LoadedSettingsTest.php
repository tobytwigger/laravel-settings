<?php

namespace Settings\Tests\Unit\Share;

use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Share\LoadedSettings;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class LoadedSettingsTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function single_settings_can_be_added(){
        $this->createSetting('setting1', 'My Setting One');
        $this->createSetting('setting2', 'My Setting Two');
        $this->createSetting('setting3', 'My Setting Three');

        $loaded = new LoadedSettings(app(SettingStore::class));
        $this->assertEquals([], $loaded->getLoadingSettings());

        $loaded->load('setting1');
        $loaded->load('setting2');
        $this->assertEquals(['setting1', 'setting2'], $loaded->getLoadingSettings());

        $loaded->load('setting3');
        $this->assertEquals(['setting1', 'setting2', 'setting3'], $loaded->getLoadingSettings());
    }

    /** @test */
    public function many_settings_can_be_added()
    {
        $this->createSetting('setting1', 'My Setting One');
        $this->createSetting('setting2', 'My Setting Two');
        $this->createSetting('setting3', 'My Setting Three');
        $this->createSetting('setting4', 'My Setting Four');
        $this->createSetting('setting5', 'My Setting Five');

        $loaded = new LoadedSettings(app(SettingStore::class));
        $this->assertEquals([], $loaded->getLoadingSettings());

        $loaded->loadMany(['setting1', 'setting2']);
        $this->assertEquals(['setting1', 'setting2'], $loaded->getLoadingSettings());

        $loaded->load('setting3');
        $this->assertEquals(['setting1', 'setting2', 'setting3'], $loaded->getLoadingSettings());

        $loaded->loadMany(['setting4', 'setting5']);
        $this->assertEquals(['setting1', 'setting2', 'setting3', 'setting4', 'setting5'], $loaded->getLoadingSettings());
    }

    /** @test */
    public function duplicates_are_ignored(){
        $this->createSetting('setting1', 'My Setting One');
        $this->createSetting('setting2', 'My Setting Two');
        $this->createSetting('setting3', 'My Setting Three');

        $loaded = new LoadedSettings(app(SettingStore::class));
        $this->assertEquals([], $loaded->getLoadingSettings());

        $loaded->loadMany(['setting1', 'setting2']);
        $this->assertEquals(['setting1', 'setting2'], $loaded->getLoadingSettings());

        $loaded->load('setting1');
        $this->assertEquals(['setting1', 'setting2'], $loaded->getLoadingSettings());

        $loaded->load('setting3');
        $this->assertEquals(['setting1', 'setting2', 'setting3'], $loaded->getLoadingSettings());

        $loaded->loadMany(['setting2', 'setting3']);
        $this->assertEquals(['setting1', 'setting2', 'setting3'], $loaded->getLoadingSettings());
    }

    /** @test */
    public function load_throws_an_exception_if_the_setting_key_is_not_found(){
        $this->expectException(SettingNotRegistered::class);
        $this->expectExceptionMessage('Setting [setting3] has not been registered.');

        $this->createSetting('setting1', 'My Setting One');
        $this->createSetting('setting2', 'My Setting Two');

        $loaded = new LoadedSettings(app(SettingStore::class));
        $loaded->load('setting1');
        $loaded->load('setting2');
        $this->assertEquals(['setting1', 'setting2'], $loaded->getLoadingSettings());

        $loaded->load('setting3');
    }

    /** @test */
    public function loadMany_throws_an_exception_if_the_setting_key_is_not_found(){
        $this->expectException(SettingNotRegistered::class);
        $this->expectExceptionMessage('Setting [setting3] has not been registered.');

        $this->createSetting('setting1', 'My Setting One');
        $this->createSetting('setting2', 'My Setting Two');

        $loaded = new LoadedSettings(app(SettingStore::class));
        $loaded->loadMany(['setting1', 'setting2']);
        $this->assertEquals(['setting1', 'setting2'], $loaded->getLoadingSettings());

        $loaded->loadMany(['setting3']);
    }

    /** @test */
    public function eagerLoad_creates_an_instance_and_loads_the_setting(){
        $this->createSetting('setting1', 'My Setting One');

        $this->assertEquals([], app(LoadedSettings::class)->getLoadingSettings());
        LoadedSettings::eagerLoad('setting1');
        $this->assertEquals(['setting1'], app(LoadedSettings::class)->getLoadingSettings());
    }

}
