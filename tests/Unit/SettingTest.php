<?php

namespace Settings\Tests\Unit;

use Settings\Setting;
use PHPUnit\Framework\Attributes\Test;
use Settings\Contracts\SettingService;
use Settings\Tests\TestCase;

class SettingTest extends TestCase
{

    #[Test]
    public function it_creates_the_underlying_class(){
        $this->assertInstanceOf(SettingService::class, Setting::getFacadeRoot());
    }

}
