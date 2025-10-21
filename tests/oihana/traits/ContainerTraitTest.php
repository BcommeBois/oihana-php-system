<?php

namespace tests\oihana\traits;

use DI\Container;

use oihana\traits\ContainerTrait;
use PHPUnit\Framework\TestCase;

class MockContainer
{
    use ContainerTrait;
}

final class ContainerTraitTest extends TestCase
{
    private MockContainer $object;

    protected function setUp(): void
    {
        $this->object = new MockContainer();
    }

    public function testContainerPropertyCanBeAssignedAndRead(): void
    {
        $container = new Container();

        $this->object->container = $container;

        $this->assertInstanceOf(Container::class, $this->object->container);
        $this->assertSame($container, $this->object->container);
    }

    public function testDefaultContainerPropertyIsUndefinedUntilAssigned(): void
    {
        $this->object->container = new Container();
        $this->assertObjectHasProperty('container', $this->object);
    }
}