<?php

namespace Settings\Tests\Unit\DatabaseSettings;

use Settings\DatabaseSettings\SavedSetting;
use Settings\Tests\TestCase;

class SavedSettingTest extends TestCase
{

    /** @test */
    public function it_can_be_created(){
        $data = SavedSetting::factory()->make()->toArray();

        $model = SavedSetting::create($data);

        $this->assertDatabaseHas(config('laravel-settings.table', 'settings'), $data);
    }

}
