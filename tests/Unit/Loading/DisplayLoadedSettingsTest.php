<?php

namespace Settings\Tests\Unit\Loading;

use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Loading\DisplayLoadedSettings;
use Settings\Loading\LoadedSettings;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class DisplayLoadedSettingsTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function toString_returns_the_settings_as_a_string(){
        $setting1 = $this->createSetting('setting1', 'My Setting One');
        $setting2 = $this->createSetting('setting2', 'My Setting Two');

        $loadedSettings = $this->prophesize(LoadedSettings::class);
        $loadedSettings->getLoadingSettings()->shouldBeCalled()->willReturn([
            'setting1', 'setting2'
        ]);

        $service = $this->prophesize(SettingService::class);
        $service->getValue('setting1')->willReturn('My Setting One');
        $service->getValue('setting2')->willReturn('My Setting Two');

        $display = new DisplayLoadedSettings($loadedSettings->reveal(), $service->reveal());

        $this->assertEquals(
            'window.ESSettings=window.ESSettings||{};ESSettings.setting1="My Setting One";ESSettings.setting2="My Setting Two";',
            $display->toString()
        );
    }

}
