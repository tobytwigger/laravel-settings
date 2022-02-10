<?php

namespace Settings\Tests\Integration\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class GetSettingControllerTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_gets_one_setting(){
        $this->createSetting('siteName', 'My Site Name');
        $response = $this->getJson(route('settings.get', ['settings' => ['siteName']]));
        $response->assertExactJson(['siteName' => 'My Site Name']);
    }

    /** @test */
    public function it_gets_many_settings(){
        $this->createSetting('siteName', 'My Site Name');
        $this->createSetting('mainColour', 'Orange');
        $this->createSetting('mailFrom', 'settings@example.com');

        $response = $this->getJson(route('settings.get', ['settings' => ['siteName', 'mainColour']]));
        $response->assertExactJson([
            'siteName' => 'My Site Name',
            'mainColour' => 'Orange',
        ]);

    }

    /** @test */
    public function it_validates_the_general_structure(){
        $response = $this->getJson(route('settings.get'));
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settings' => ['The settings field is required']
        ]);

        $response = $this->getJson(route('settings.get', ['settings' => 'not an array']));
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settings' => ['The settings must be an array.']
        ]);
    }

    /** @test */
    public function it_validates_the_setting_keys(){
        $this->createSetting('siteName', 'My Site Name');
        $this->createSetting('siteNameTwo', 'My Site Name Two');

        $response = $this->getJson(route('settings.get', ['settings' => ['siteName', 'siteNameTwo', 'siteNameThree']]));
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settings.2' => ['The siteNameThree setting key does not exist.']
        ]);
    }

    /** @test */
    public function it_authorizes_if_you_cant_read_to_any_setting(){
        $this->createSetting('siteName', 'My Site Name', canRead: true, canWrite: false);
        $this->createSetting('siteNameTwo', 'My Site Name Two', canRead: false, canWrite: false);

        $response = $this->getJson(route('settings.get', [
            'settings' => ['siteName', 'siteNameTwo']
        ]));
        $response->assertStatus(403);
        $response->decodeResponseJson()->assertExact([
            'message' => 'You do not have permission to read the [siteNameTwo] setting.'
        ]);
    }

    /** @test */
    public function it_authorizes_before_validating(){
        $this->createSetting('siteName', 'My Site Name', ['string'], canRead: false, canWrite: false);

        $response = $this->getJson(route('settings.get', ['settings' => ['siteName', 'notASetting']]));
        $response->assertStatus(403);
        $response->assertJsonMissingValidationErrors();
    }

}
