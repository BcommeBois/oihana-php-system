<?php

namespace oihana\reflections\traits;

use PHPUnit\Framework\TestCase;

final class ReflectionTraitTest extends TestCase
{
    public function testGetConstants(): void
    {
        $object = new class {
            use ReflectionTrait;
        };

        $classWithConstants = new class {
            public const string FOO = 'bar';
        };

        $constants = $object->getConstants($classWithConstants::class);
        $this->assertSame(['FOO' => 'bar'], $constants);
    }

    public function testGetPublicProperties(): void
    {
        $object = new class
        {
            use ReflectionTrait;
        };

        $classWithProperties = new class {
            public string $name;
        };

        $props = $object->getPublicProperties($classWithProperties::class);
        $this->assertCount(1, $props);
        $this->assertSame('name', $props[0]->getName());
    }

    public function testGetShortName(): void
    {
        $object = new class {
            use ReflectionTrait;
        };

        $short = $object->getShortName(\stdClass::class);
        $this->assertSame('stdClass', $short);
    }

    public function testHydrate(): void
    {
        $object = new class {
            use ReflectionTrait;
        };

        $className = new class {
            public string $name;
        };

        $hydrated = $object->hydrate(['name' => 'Alice'], $className::class);
        $this->assertSame('Alice', $hydrated->name);
    }

    public function testJsonSerializeFromPublicProperties(): void
    {
        $obj = new class
        {
            use ReflectionTrait;

            public string $name = 'Book';
            public ?string $desc = null;
        };

        $data = $obj->jsonSerializeFromPublicProperties($obj::class);
        $this->assertSame(['name' => 'Book', 'desc' => null], $data);

        $dataReduced = $obj->jsonSerializeFromPublicProperties($obj::class, true);
        $this->assertSame(['name' => 'Book'], $dataReduced);
    }

    public function testGetMethodParameters(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(string $name, int $age = 0) {}
        };

        $params = $obj->getMethodParameters($obj::class, 'demo');
        $this->assertCount(2, $params);
        $this->assertSame('name', $params[0]->getName());
        $this->assertSame('age', $params[1]->getName());
    }

    public function testGetParameterType(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(string $name) {}
        };

        $type = $obj->getParameterType($obj::class, 'demo', 'name');
        $this->assertSame('string', $type);
    }

    public function testGetParameterDefaultValue(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(string $name = 'default') {}
        };

        $default = $obj->getParameterDefaultValue($obj::class, 'demo', 'name');
        $this->assertSame('default', $default);
    }

    public function testHasParameter(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(string $name) {}
        };

        $this->assertTrue($obj->hasParameter($obj::class, 'demo', 'name'));
        $this->assertFalse($obj->hasParameter($obj::class, 'demo', 'unknown'));
    }

    public function testIsParameterNullable(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(?string $name) {}
        };

        $this->assertTrue($obj->isParameterNullable($obj::class, 'demo', 'name'));
    }

    public function testIsParameterOptional(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(string $name = 'x') {}
        };

        $this->assertTrue($obj->isParameterOptional($obj::class, 'demo', 'name'));
    }

    public function testIsParameterVariadic(): void
    {
        $obj = new class {
            use ReflectionTrait;
            public function demo(string ...$args) {}
        };

        $this->assertTrue($obj->isParameterVariadic($obj::class, 'demo', 'args'));
    }
}