<?php

namespace Settings\Tests\Unit\Decorators;

use Illuminate\Support\Facades\Crypt;
use Prophecy\Argument;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Contracts\Setting;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\EncryptionDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class EncryptionDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_decrypts_a_value_if_retrieved_with_the_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getValueWithId($setting, 5)->shouldBeCalledTimes(1)->willReturn(Crypt::encrypt('test-value', false));

        $decorator = new EncryptionDecorator($baseService->reveal(), Crypt::getFacadeRoot());

        $this->assertEquals('test-value', $decorator->getValueWithId($setting, 5));
    }

    /** @test */
    public function it_decrypts_a_default_value(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getDefaultValue($setting)->shouldBeCalledTimes(1)->willReturn(Crypt::encrypt('test-value', false));

        $decorator = new EncryptionDecorator($baseService->reveal(), Crypt::getFacadeRoot());

        $this->assertEquals('test-value', $decorator->getDefaultValue($setting));
    }

    /** @test */
    public function it_encrypts_a_value_if_set_with_the_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->setValue($setting, Argument::that(fn($arg) => Crypt::decrypt($arg, false) === 'new-value'), 5)->shouldBeCalled();

        $decorator = new EncryptionDecorator($baseService->reveal(), Crypt::getFacadeRoot());

        $decorator->setValue($setting, 'new-value', 5);
    }

    /** @test */
    public function it_encrypts_a_default_value_when_set(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->setDefaultValue($setting, Argument::that(fn($arg) => Crypt::decrypt($arg, false) === 'new-value'))->shouldBeCalled();

        $decorator = new EncryptionDecorator($baseService->reveal(), Crypt::getFacadeRoot());

        $decorator->setDefaultValue($setting, 'new-value');
    }

}

