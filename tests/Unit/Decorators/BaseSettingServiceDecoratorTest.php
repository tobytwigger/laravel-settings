<?php

namespace Settings\Tests\Unit\Decorators;

use FormSchema\Generator\Field;
use PHPUnit\Framework\Attributes\Test;
use Settings\Anonymous\AnonymousSetting;
use Settings\Contracts\Setting;
use Settings\Contracts\SettingService;
use Settings\Contracts\SettingStore;
use Settings\Decorators\AppNotBootedDecorator;
use Settings\Decorators\BaseSettingServiceDecorator;
use Settings\Exceptions\AppNotBooted;
use Settings\Store\Query;
use Settings\Tests\TestCase;
use Settings\Tests\Traits\CreatesSettings;

class BaseSettingServiceDecoratorTest extends TestCase
{
    use CreatesSettings;

    private \Prophecy\Prophecy\ObjectProphecy $baseService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->baseService = $this->prophesize(SettingService::class);
    }

    private function baseService(): SettingService
    {
        return $this->baseService->reveal();
    }

    #[Test]
    public function withGroup_proxies_the_underlying_service()
    {
        $this->baseService->withGroup('GroupName')->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->withGroup('GroupName');
    }

    #[Test]
    public function withAnyGroups_proxies_the_underlying_service()
    {
        $this->baseService->withAnyGroup(['group1', 'group2'])->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->withAnyGroup(['group1', 'group2']);
    }

    #[Test]
    public function withAllGroups_proxies_the_underlying_service()
    {
        $this->baseService->withAllGroups(['group1', 'group2'])->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->withAllGroups(['group1', 'group2']);
    }

    #[Test]
    public function withType_proxies_the_underlying_service()
    {
        $this->baseService->withType('Type1')->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->withType('Type1');
    }

    #[Test]
    public function withGlobalType_proxies_the_underlying_service()
    {
        $this->baseService->withGlobalType()->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->withGlobalType();
    }

    #[Test]
    public function withUserType_proxies_the_underlying_service()
    {
        $this->baseService->withUserType()->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->withUserType();
    }

    #[Test]
    public function search_proxies_the_underlying_service()
    {
        $this->baseService->search()->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->search();
    }

    #[Test]
    public function alias_proxies_the_underlying_service()
    {
        $this->baseService->alias('alias', 'key')->shouldBeCalled();
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->alias('alias', 'key');
    }

    #[Test]
    public function store_proxies_the_underlying_service()
    {
        $this->baseService->store()->shouldBeCalled()->willReturn($this->prophesize(SettingStore::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->store();
    }

    #[Test]
    public function register_proxies_the_underlying_service()
    {
        $this->baseService->register([], ['group1'])->shouldBeCalled();
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->register([], ['group1']);
    }

    #[Test]
    public function registerGroup_proxies_the_underlying_service()
    {
        $this->baseService->registerGroup('key', 'title', 'subtitle')->shouldBeCalled();
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->registerGroup('key', 'title', 'subtitle');
    }

    #[Test]
    public function getValue_proxies_the_underlying_service()
    {
        $this->baseService->getValue('key', 2)->shouldBeCalled()->willReturn($this->prophesize(Query::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->getValue('key', 2);
    }

    #[Test]
    public function setDefaultValue_proxies_the_underlying_service()
    {
        $this->baseService->setDefaultValue('key1', 1)->shouldBeCalled();
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->setDefaultValue('key1', 1);
    }

    #[Test]
    public function setValue_proxies_the_underlying_service()
    {
        $this->baseService->setValue('key3', 'test', 1)->shouldBeCalled();
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->setValue('key3', 'test', 1);
    }

    #[Test]
    public function getSettingByKey_proxies_the_underlying_service()
    {
        $this->baseService->getSettingByKey('key4')->shouldBeCalled()->willReturn($this->prophesize(Setting::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->getSettingByKey('key4');
    }

    #[Test]
    public function create_proxies_the_underlying_service()
    {
        $this->baseService->create('customType', 'key3', 'val1', Field::text('key3'), ['group3', 'group1'], 'string', null)->shouldBeCalled()->willReturn($this->prophesize(AnonymousSetting::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->create('customType', 'key3', 'val1', Field::text('key3'), ['group3', 'group1'], 'string', null);
    }

    #[Test]
    public function createUser_proxies_the_underlying_service()
    {
        $this->baseService->createUser('key3', 'val1', Field::text('key3'), ['group3', 'group1'], 'string', null)->shouldBeCalled()->willReturn($this->prophesize(AnonymousSetting::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->createUser('key3', 'val1', Field::text('key3'), ['group3', 'group1'], 'string', null);
    }

    #[Test]
    public function createGlobal_proxies_the_underlying_service()
    {
        $this->baseService->createGlobal('key3', 'val1', Field::text('key3'), ['group3', 'group1'], 'string', null)->shouldBeCalled()->willReturn($this->prophesize(AnonymousSetting::class)->reveal());
        $decorator = new BaseSettingServiceDecorator($this->baseService());
        $decorator->createGlobal('key3', 'val1', Field::text('key3'), ['group3', 'group1'], 'string', null);
    }

}

