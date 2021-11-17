<?php

namespace Settings\Tests\Unit\Types;

use FormSchema\Schema\Field;
use Settings\Tests\TestCase;
use Settings\Types\UserSetting;

class UserSettingTest extends TestCase
{

    /** @test */
    public function resolveId_returns_the_user_id(){
        $guard = $this->prophesize(\Illuminate\Contracts\Auth\Guard::class);
        $guard->id()->shouldBeCalled()->willReturn(4);
        $this->instance('auth', $guard->reveal());

        $this->assertEquals(4, (new UserSettingTestDummySetting())->resolveId());
    }

    /** @test */
    public function resolveId_returns_null_if_no_user_is_logged_in(){
        $this->assertNull((new UserSettingTestDummySetting())->resolveId());
    }

    /** @test */
    public function resolveId_can_be_overridden(){
        $guard = $this->prophesize(\Illuminate\Contracts\Auth\Guard::class);
        $guard->id()->willReturn(4);
        $this->instance('auth', $guard->reveal());

        UserSettingTestDummySetting::$resolveUserUsing = fn() => 6;

        $this->assertEquals(6, (new UserSettingTestDummySetting())->resolveId());

    }

    /** @test */
    public function the_type_is_correct()
    {
        $this->assertEquals(UserSetting::class, (new UserSettingTestDummySetting())->type());
    }

}

class UserSettingTestDummySetting extends UserSetting
{

    public function defaultValue(): mixed
    {
    }

    public function fieldOptions(): Field
    {
    }

    public function rules(): array|string
    {
    }

    protected function groups(): array
    {
    }
}
