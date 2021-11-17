<?php

namespace Settings\Tests\Integration\DatabaseSettings;

use Settings\Contracts\Setting;
use Settings\DatabaseSettings\DatabaseSettingRepository;
use Settings\DatabaseSettings\SavedSetting;
use Settings\Exceptions\PersistedSettingNotFound;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class DatabaseSettingRepositoryTest extends TestCase
{
    use CreatesSettings;

    private DatabaseSettingRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new DatabaseSettingRepository();
    }

    /** @test */
    public function getValueWithId_returns_value_of_the_setting_if_a_setting_is_found(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');
        SavedSetting::factory(['key' => 'siteName', 'value' => 'Site Name 2', 'model_id' => 5])->create();

        $this->assertEquals('Site Name 2', $this->repository->getValueWithId($setting, 5));
    }

    /** @test */
    public function getValueWithId_throws_an_exception_if_the_setting_is_not_found(){
        $this->expectException(PersistedSettingNotFound::class);
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $this->repository->getValueWithId($setting, 5);
    }

    /** @test */
    public function getDefaultValue_returns_default_of_the_setting_if_a_setting_is_found(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');
        SavedSetting::factory(['key' => 'siteName', 'value' => 'Site Name 2', 'model_id' => null])->create();

        $this->assertEquals('Site Name 2', $this->repository->getDefaultValue($setting));
    }

    /** @test */
    public function getDefaultValue_throws_an_exception_if_the_setting_default_is_not_found(){
        $this->expectException(PersistedSettingNotFound::class);
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $this->repository->getDefaultValue($setting);
    }

    /** @test */
    public function setDefaultValue_creates_a_new_entry_without_a_model_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $this->assertDatabaseCount('settings', 0);

        $this->repository->setDefaultValue($setting, 'Site Name 2');

        $this->assertDatabaseHas('settings', [
            'key' => 'siteName',
            'value' => 'Site Name 2',
            'model_id' => null
        ]);
    }

    /** @test */
    public function setDefaultValue_updates_an_old_entry_if_it_exists(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        SavedSetting::factory(['key' => 'siteName', 'value' => 'Site Name 2', 'model_id' => null])->create();

        $this->assertDatabaseHas('settings', [
            'key' => 'siteName',
            'value' => 'Site Name 2',
            'model_id' => null
        ]);

        $this->repository->setDefaultValue($setting, 'Site Name 3');

        $this->assertDatabaseHas('settings', [
            'key' => 'siteName',
            'value' => 'Site Name 3',
            'model_id' => null
        ]);
        $this->assertDatabaseCount('settings', 1);

    }

    /** @test */
    public function setValue_creates_a_new_entry_without_a_model_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $this->assertDatabaseCount('settings', 0);

        $this->repository->setValue($setting, 'Site Name 2', 5);

        $this->assertDatabaseHas('settings', [
            'key' => 'siteName',
            'value' => 'Site Name 2',
            'model_id' => 5
        ]);
    }

    /** @test */
    public function setValue_updates_an_old_entry_if_it_exists(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        SavedSetting::factory(['key' => 'siteName', 'value' => 'Site Name 2', 'model_id' => 5])->create();

        $this->assertDatabaseCount('settings', 1);

        $this->repository->setValue($setting, 'Site Name 3', 5);

        $this->assertDatabaseHas('settings', [
            'key' => 'siteName',
            'value' => 'Site Name 3',
            'model_id' => 5
        ]);
        $this->assertDatabaseCount('settings', 1);

    }

}
