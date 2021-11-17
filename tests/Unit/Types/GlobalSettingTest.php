<?php

namespace Settings\Tests\Unit\Types;

use FormSchema\Schema\Field;
use Settings\Tests\TestCase;
use Settings\Types\GlobalSetting;

class GlobalSettingTest extends TestCase
{

    /** @test */
    public function resolveId_returns_null(){
        $this->assertNull((new GlobalSettingTestDummySetting())->resolveId());
    }

    /** @test */
    public function the_type_is_correct()
    {
        $this->assertEquals(GlobalSetting::class, (new GlobalSettingTestDummySetting())->type());
    }

}

class GlobalSettingTestDummySetting extends GlobalSetting
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
