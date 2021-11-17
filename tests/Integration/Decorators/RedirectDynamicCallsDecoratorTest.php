<?php

namespace Settings\Tests\Integration\Decorators;

use Illuminate\Validation\ValidationException;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class RedirectDynamicCallsDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function different_setting_casing_works(){
        $setting = $this->createSetting('site-name-one', 'Site Name 1', ['string']);
        $setting = $this->createSetting('SiteNameTwo', 'Site Name 2', ['string']);
        $setting = $this->createSetting('SiteNameThree', 'Site Name 3', ['string']);
        $setting = $this->createSetting('siteNameFour', 'Site Name 4', ['string']);
        $setting = $this->createSetting('site_name_five', 'Site Name 5', ['string']);

        $this->assertEquals('Site Name 1', Setting::getSiteNameOne());
        $this->assertEquals('Site Name 2', Setting::getSiteNameTwo());
        $this->assertEquals('Site Name 3', Setting::getSiteNameThree());
        $this->assertEquals('Site Name 4', Setting::getSiteNameFour());
        $this->assertEquals('Site Name 5', Setting::getSiteNameFive());
    }

    /** @test */
    public function aliases_can_be_set_and_got_from_the_settings_service_using_shorthand(){
        $setting = $this->createSetting('\My\Namespace\SiteName', 'Site Name 1', ['string']);
        Setting::alias('siteName', '\My\Namespace\SiteName');

        $this->assertEquals('Site Name 1', Setting::getSiteName());

        Setting::setSiteName('SiteName2');

        $this->assertEquals('SiteName2', Setting::getSiteName());
    }

    /** @test */
    public function values_set_through_aliases_still_get_validated(){
        $this->expectException(ValidationException::class);

        $setting = $this->createSetting('\My\Namespace\SiteName', 'Site Name 1', ['string']);
        Setting::alias('siteName', '\My\Namespace\SiteName');

        Setting::setSiteName(['an' => 'error']);
    }

}
