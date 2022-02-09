<?php

namespace Settings\Tests\Integration\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class UpdateSettingControllerTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_updates_settings(){
        $this->createSetting('siteName', 'My Site Name');

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => 'My new site name'
            ]
        ]);

        $this->assertEquals('My new site name', Setting::getValue('siteName'));
    }

    /** @test */
    public function it_updates_many_settings(){
        $this->createSetting('siteName', 'My Site Name');
        $this->createSetting('mainColour', 'Orange');
        $this->createSetting('mailFrom', 'settings@example.com');

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => 'My new site name',
                'mainColour' => 'Blue',
                'mailFrom' => 'new-settings@example.co.uk'
            ]
        ]);

        $this->assertEquals('My new site name', Setting::getValue('siteName'));
        $this->assertEquals('Blue', Setting::getValue('mainColour'));
        $this->assertEquals('new-settings@example.co.uk', Setting::getValue('mailFrom'));
    }

    /** @test */
    public function it_returns_the_updated_values(){
        $this->createSetting('siteName', 'My Site Name');
        $this->createSetting('mainColour', 'Orange');
        $this->createSetting('mailFrom', 'settings@example.com');

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => 'My new site name',
                'mainColour' => 'Blue',
                'mailFrom' => 'new-settings@example.co.uk'
            ]
        ]);

        $response->decodeResponseJson()->assertExact([
            'siteName' => 'My new site name',
            'mainColour' => 'Blue',
            'mailFrom' => 'new-settings@example.co.uk'
        ]);
    }

    /** @test */
    public function it_validates_the_general_structure(){
        $this->createSetting('siteName', 'My Site Name');

        $response = $this->postJson(route('settings.update'), [
            'siteName' => 'My new site name'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settings' => ['The settings field is required']
        ]);
        $this->assertEquals('My Site Name', Setting::getValue('siteName'));

        $response = $this->postJson(route('settings.update'), [
            'settings' => 'This should really be an array'
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settings' => ['The settings must be an array.']
        ]);
        $this->assertEquals('My Site Name', Setting::getValue('siteName'));
    }

    /** @test */
    public function it_validates_the_setting_keys(){
        $this->createSetting('siteName', 'My Site Name');
        $this->createSetting('siteNameTwo', 'My Site Name Two');

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => 'My new site name',
                'siteNameTwo' => 'My new site name two',
                'siteNameThree' => 'My new site name three',
            ]
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'settings.siteNameThree' => ['The settings.siteNameThree setting key does not exist.']
        ]);
        $this->assertEquals('My Site Name', Setting::getValue('siteName'));
        $this->assertEquals('My Site Name Two', Setting::getValue('siteNameTwo'));
    }

    /** @test */
    public function it_validates_the_setting_values(){
        $this->createSetting('siteName', 'My Site Name', ['string']);

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => ['this is' => 'an array'],
            ]
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'siteName' => ['The site name must be a string.'],
        ]);
        $this->assertEquals('My Site Name', Setting::getValue('siteName'));
    }

    /** @test */
    public function it_validates_multiple_setting_values(){
        $this->createSetting('siteName', 'My Site Name', ['string']);
        $this->createSetting('siteNameTwo', 'My Site Name Two', ['array']);
        $this->createSetting('siteNameThree', 'My Site Name Three', ['string', 'min:3']);

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => ['this is' => 'an array'],
                'siteNameTwo' => 'This should be an array',
                'siteNameThree' => 'No'
            ]
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'siteName' => ['The site name must be a string.'],
            'siteNameTwo' => ['The site name two must be an array.'],
            'siteNameThree' => ['The site name three must be at least 3 characters.']
        ]);
        $this->assertEquals('My Site Name', Setting::getValue('siteName'));
    }

    /** @test */
    public function it_authorizes_if_you_cant_write_to_any_setting(){
        $this->createSetting('siteName', 'My Site Name', canRead: true, canWrite: true);
        $this->createSetting('siteNameTwo', 'My Site Name Two', canRead: true, canWrite: false);

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => 'My new site name',
                'siteNameTwo' => 'My new site name two'
            ]
        ]);
        $response->assertStatus(403);
        $response->decodeResponseJson()->assertExact([
            'message' => 'You do not have permission to update the [siteNameTwo] setting.'
        ]);
        $this->assertEquals('My Site Name', Setting::getValue('siteName'));
        $this->assertEquals('My Site Name Two', Setting::getValue('siteNameTwo'));
    }

    /** @test */
    public function you_can_update_write_only_settings(){
        $this->createSetting('siteName', 'My Site Name', canRead: false, canWrite: true);
        $this->createSetting('siteNameTwo', 'My Site Name Two', canRead: false, canWrite: true);

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => 'My new site name',
                'siteNameTwo' => 'My new site name two'
            ]
        ]);
        $response->assertStatus(200);

        $this->assertEquals('My new site name', Crypt::decrypt(SavedSetting::where('key', 'siteName')->firstOrFail()->value));
        $this->assertEquals('My new site name two', Crypt::decrypt(SavedSetting::where('key', 'siteNameTwo')->firstOrFail()->value));
    }

    /** @test */
    public function it_authorizes_before_validating(){
        $this->createSetting('siteName', 'My Site Name', ['string'], canRead: false, canWrite: false);

        $response = $this->postJson(route('settings.update'), [
            'settings' => [
                'siteName' => ['this is' => 'an array'],
            ]
        ]);
        $response->assertStatus(403);
        $response->assertJsonMissingValidationErrors();
    }

}
