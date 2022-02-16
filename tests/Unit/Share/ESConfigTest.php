<?php

namespace Settings\Tests\Unit\Share;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\URL;
use Settings\Share\ESConfig;
use Settings\Tests\TestCase;

class ESConfigTest extends TestCase
{

    /** @test */
    public function it_calculates_all_the_config(){
        $repository = $this->prophesize(Repository::class);
        $repository->get('laravel-settings.routes.api.enabled', true)->willReturn(true);

        $esConfig = new ESConfig($repository->reveal());

        $this->assertEquals([
            'api_enabled' => true,
            'api_get_url' => 'http://localhost/setting',
            'api_update_url' => 'http://localhost/setting',
        ], $esConfig->getConfig());
    }

}
