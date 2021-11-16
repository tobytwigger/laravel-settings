<?php

namespace Settings\Tests\Integration\Decorators;

use FormSchema\Generator\Field;
use FormSchema\Schema\Form;
use Settings\Anonymous\AnonymousSetting;
use Settings\Anonymous\AnonymousSettingFactory;
use Settings\Collection\SettingCollection;
use Settings\Contracts\Setting;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Types\GlobalSetting;
use Settings\Types\UserSettings;

class AppNotBootedDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_throws_an_exception_if_the_app_is_not_booted()
    {
        $this->expectException(AppNotBooted::class);
        AppNotBootedDecorator::$booted = false;

        \Settings\Setting::withType('type')->get();
    }

    /** @test */
    public function it_does_not_throw_an_exception_if_the_app_is_booted()
    {
        AppNotBootedDecorator::$booted = true;

        $val = \Settings\Setting::withType('type')->get();

        $this->assertCount(0, $val);
    }
}

