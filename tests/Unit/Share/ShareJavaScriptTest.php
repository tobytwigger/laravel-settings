<?php

namespace Settings\Tests\Unit\Share;

use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Share\ESConfig;
use Settings\Share\ShareJavaScript;
use Settings\Share\LoadedSettings;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class ShareJavaScriptTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function toString_returns_the_settings_and_config_as_a_string(){
        $setting1 = $this->createSetting('setting1', 'My Setting One');
        $setting2 = $this->createSetting('setting2', 'My Setting Two');

        $loadedSettings = $this->prophesize(LoadedSettings::class);
        $loadedSettings->getLoadingSettings()->shouldBeCalled()->willReturn([
            'setting1', 'setting2'
        ]);

        $esConfig = $this->prophesize(ESConfig::class);
        $esConfig->getConfig()->willReturn([
            'config1' => 'value1', 'config2' => 'value2'
        ]);

        $service = $this->prophesize(SettingService::class);
        $service->getValue('setting1')->willReturn('My Setting One');
        $service->getValue('setting2')->willReturn('My Setting Two');

        $display = new ShareJavaScript($loadedSettings->reveal(), $service->reveal(), $esConfig->reveal());

        $this->assertEquals(
            'window.ESSettings=window.ESSettings||{};window.ESSettingsConfig=window.ESSettingsConfig||{};ESSettings.setting1="My Setting One";ESSettings.setting2="My Setting Two";'
            . 'ESSettingsConfig.config1="value1";ESSettingsConfig.config2="value2";',
            $display->toString()
        );
    }

}
