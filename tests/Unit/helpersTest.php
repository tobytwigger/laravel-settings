<?php

namespace Settings\Tests\Unit;

use Settings\Contracts\SettingService;
use Settings\Tests\TestCase;

class helpersTest extends TestCase
{

    /** @test */
    public function settings_returns_an_instance_of_the_translation_manager_when_given_no_parameters()
    {
        $this->assertInstanceOf(SettingService::class, \settings());
    }

}
