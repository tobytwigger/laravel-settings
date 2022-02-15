<?php

namespace Settings\Tests\Unit\Http\Middleware;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Settings\Http\Middleware\ShareSettingsWithJs;
use Settings\Share\LoadedSettings;
use Settings\Tests\TestCase;

class ShareSettingsWithJsTest extends TestCase
{

    /** @test */
    public function it_shares_settings_from_config(){
        $repository = $this->prophesize(Repository::class);
        $repository->get('laravel-settings.js.autoload', [])->shouldBeCalled()->willReturn([
            'setting1', 'setting2', 'setting3'
        ]);

        $loadedSettings = $this->prophesize(LoadedSettings::class);
        $loadedSettings->loadMany(['setting1', 'setting2', 'setting3']);

        $middleware = new ShareSettingsWithJs($repository->reveal(), $loadedSettings->reveal());
        $middleware->handle($this->prophesize(Request::class)->reveal(), function($request) {});
    }

}
