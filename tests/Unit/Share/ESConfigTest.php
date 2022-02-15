<?php

namespace Settings\Tests\Unit\Share;

use Settings\Contracts\SettingStore;
use Settings\Exceptions\SettingNotRegistered;
use Settings\Share\ESConfig;
use Settings\Share\LoadedSettings;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class ESConfigTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function single_config_can_be_added(){
        $config = new ESConfig();
        $this->assertEquals([], $config->getConfig());

        $config->add('config1', 'value1');
        $config->add('config2', 'value2');
        $this->assertEquals(['config1' => 'value1', 'config2' => 'value2'], $config->getConfig());

        $config->add('config3', 'value3');
        $this->assertEquals(['config1' => 'value1', 'config2' => 'value2', 'config3' => 'value3'], $config->getConfig());
    }

    /** @test */
    public function many_config_can_be_added(){
        $config = new ESConfig();
        $this->assertEquals([], $config->getConfig());

        $config->addMany(['config1' => 'value1', 'config2' => 'value2']);
        $this->assertEquals(['config1' => 'value1', 'config2' => 'value2'], $config->getConfig());

        $config->addMany(['config3' => 'value3']);
        $this->assertEquals(['config1' => 'value1', 'config2' => 'value2', 'config3' => 'value3'], $config->getConfig());

        $config->addMany(['config4' => 'value4', 'config5' => 'value5']);
        $this->assertEquals(['config1' => 'value1', 'config2' => 'value2', 'config3' => 'value3', 'config4' => 'value4', 'config5' => 'value5'], $config->getConfig());
    }

    /** @test */
    public function duplicates_update_the_value(){
        $config = new ESConfig();
        $this->assertEquals([], $config->getConfig());

        $config->addMany(['config1' => 'value1', 'config2' => 'value2']);
        $this->assertEquals(['config1' => 'value1', 'config2' => 'value2'], $config->getConfig());

        $config->addMany(['config1' => 'value3']);
        $this->assertEquals(['config1' => 'value3', 'config2' => 'value2'], $config->getConfig());
    }

    /** @test */
    public function share_creates_an_instance_and_adds_the_setting(){
        ESConfig::share('config1', 'value1');

        $this->assertEquals(['config1' => 'value1'], app(ESConfig::class)->getConfig());
    }

}
