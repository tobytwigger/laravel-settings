<?php

namespace Settings\Tests\Unit;

use Settings\Setting;
use Settings\Contracts\SettingService;
use Settings\Tests\TestCase;

class SettingTest extends TestCase
{

    /** @test */
    public function it_creates_the_underlying_class(){
        $this->assertInstanceOf(SettingService::class, Setting::getFacadeRoot());
    }

}
