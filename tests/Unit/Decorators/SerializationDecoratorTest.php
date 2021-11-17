<?php

namespace Settings\Tests\Unit\Decorators;

use Prophecy\Argument;
use Settings\Contracts\CastsSettingValue;
use Settings\Contracts\PersistedSettingRepository;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\SerializationDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Setting;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;
use Settings\Tests\Traits\FakeSetting;

class SerializationDecoratorTest extends TestCase
{
    use CreatesSettings;

    /** @test */
    public function it_unserializes_a_value_if_retrieved_with_the_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getValueWithId($setting, 5)->shouldBeCalledTimes(1)->willReturn(serialize('test-value'));

        $decorator = new SerializationDecorator($baseService->reveal());

        $this->assertEquals('test-value', $decorator->getValueWithId($setting, 5));
    }

    /** @test */
    public function it_unserializes_a_default_value(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->getDefaultValue($setting)->shouldBeCalledTimes(1)->willReturn(serialize('test-value'));

        $decorator = new SerializationDecorator($baseService->reveal());

        $this->assertEquals('test-value', $decorator->getDefaultValue($setting));
    }

    /** @test */
    public function it_serializes_a_value_if_set_with_the_id(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->setValue($setting, Argument::that(fn($arg) => unserialize($arg) === 'new-value'), 5)->shouldBeCalled();

        $decorator = new SerializationDecorator($baseService->reveal());

        $decorator->setValue($setting, 'new-value', 5);
    }

    /** @test */
    public function it_serializes_a_default_value_when_set(){
        $setting = $this->createSetting('siteName', 'Site Name 1', 'string');

        $baseService = $this->prophesize(PersistedSettingRepository::class);
        $baseService->setDefaultValue($setting, Argument::that(fn($arg) => unserialize($arg) === 'new-value'))->shouldBeCalled();

        $decorator = new SerializationDecorator($baseService->reveal());

        $decorator->setDefaultValue($setting, 'new-value');

    }

    /** @test */
    public function settings_can_control_serialization(){
        $object = new class {
            public $test = 1;
            public $otherTest = ['testing', '123'];
        };
        $setting = new DummySettingTestWithCasting('key-object', null);
        Setting::register($setting);

        Setting::setValue('key-object', $object);


        $this->assertEquals(
            1,
            Setting::getValue('key-object')->test
        );
        $this->assertEquals(
            ['testing', '123'],
            Setting::getValue('key-object')->otherTest
        );
    }

}

class DummySettingTestWithCasting extends FakeSetting implements CastsSettingValue
{

    public function castToString($value): string
    {
        return json_encode([
            'test' => $value->test,
            'other' => $value->otherTest
        ]);
    }

    public function castToValue(string $value): mixed
    {
        $value = json_decode($value, true);
        $class = new class {
            public $test;
            public $otherTest;
        };
        $class->test = $value['test'];
        $class->otherTest = $value['other'];
        return $class;
    }


}
