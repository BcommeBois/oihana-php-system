<?php

namespace oihana\reflections;

use InvalidArgumentException;
use oihana\reflections\mocks\MockAddress;
use oihana\reflections\mocks\MockEnum;
use oihana\reflections\mocks\MockGeo;
use oihana\reflections\mocks\MockPolymorphicContainer;
use oihana\reflections\mocks\MockUser;
use oihana\reflections\mocks\MockWithRenameKey;
use PHPUnit\Framework\TestCase;
use ReflectionClassConstant;
use ReflectionException;
use ReflectionMethod;

class ReflectionTest extends TestCase
{
    private Reflection $reflection;

    protected function setUp(): void
    {
        $this->reflection = new Reflection();
    }

    /**
     * @throws ReflectionException
     */
    public function testShortName()
    {
        $this->assertEquals('MockUser', $this->reflection->shortName(MockUser::class));
    }

    /**
     * @throws ReflectionException
     */
    public function testConstants()
    {
        $constants = $this->reflection->constants(MockEnum::class);
        $this->assertArrayHasKey('ACTIVE', $constants);
        $this->assertEquals('active', $constants['ACTIVE']);
    }

    /**
     * @throws ReflectionException
     */
    public function testMethods()
    {
        $methods = $this->reflection->methods(MockUser::class);
        $methodNames = array_map(fn($m) => $m->getName(), $methods);
        $this->assertContains('getName', $methodNames);
    }

    /**
     * @throws ReflectionException
     */
    public function testProperties()
    {
        $properties = $this->reflection->properties(MockUser::class);
        $propertyNames = array_map(fn($p) => $p->getName(), $properties);
        $this->assertContains('name', $propertyNames);
        $this->assertNotContains('id', $propertyNames); // private
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionCaching()
    {
        $ref1 = $this->reflection->reflection(MockUser::class);
        $ref2 = $this->reflection->reflection(MockUser::class);
        $this->assertSame($ref1, $ref2);
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrateFlat()
    {
        $data = ['name' => 'Alice'];
        $user = $this->reflection->hydrate($data, MockUser::class);
        $this->assertInstanceOf(MockUser::class, $user);
        $this->assertEquals('Alice', $user->name);
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrateNested()
    {
        $data = [
            'name' => 'Bob',
            'address' => [
                'city' => 'Lyon'
            ]
        ];
        $user = $this->reflection->hydrate($data, MockUser::class);
        $this->assertInstanceOf(MockAddress::class, $user->address);
        $this->assertEquals('Lyon', $user->address->city);
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrateArrayOfObjects()
    {
        $data =
        [
            'locations' =>
            [
                [ 'city' => 'Paris'  ],
                [ 'city' => 'Berlin' ],
            ]
        ];
        $geo = $this->reflection->hydrate($data, MockGeo::class);
        $this->assertCount(2, $geo->locations);
        $this->assertEquals('Berlin', $geo->locations[1]->city);
    }

    public function testHydrateArrayOfPolymorphicObjectsWithHydrateWith()
    {
        $data = [
            'items' => [
                [ '@type' => 'MockAddress', 'city' => 'Nice' ],
                [ 'type' => 'MockUser', 'name' => 'Zoe' ],
                [ 'city' => 'Unknown' ], // Should fallback to first class
            ]
        ];

        $result = $this->reflection->hydrate($data, MockPolymorphicContainer::class);

        $this->assertCount(3, $result->items);
        $this->assertInstanceOf(MockAddress::class, $result->items[0]);
        $this->assertEquals('Nice', $result->items[0]->city);
        $this->assertInstanceOf(MockUser::class, $result->items[1]);
        $this->assertEquals('Zoe', $result->items[1]->name);
        $this->assertInstanceOf(MockAddress::class, $result->items[2]); // fallback to first class
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrateInvalidClassThrows()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->reflection->hydrate([], 'NonExistentClass');
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrateNonNullablePropertyWithNullThrows()
    {
        $this->expectException(InvalidArgumentException::class);

        $data = ['name' => null]; // Supposons que 'name' est non nullable
        $this->reflection->hydrate($data, MockUser::class);
    }

    /**
     * @throws ReflectionException
     */
    public function testHydrateWithHydrateKeyAttribute()
    {
        $data = ['user_name' => 'Charlie'];
        $user = $this->reflection->hydrate( $data , MockWithRenameKey::class ) ;
        $this->assertEquals('Charlie' , $user->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testClassNameAnonymousClass()
    {
        $anon = new class {};
        $name = $this->reflection->shortName($anon);
        $this->assertIsString($name);
        $this->assertNotEmpty($name);
    }

    /**
     * @throws ReflectionException
     */
    public function testConstantsWithPrivateFilter()
    {
        $constants = $this->reflection->constants(MockEnum::class, ReflectionClassConstant::IS_PRIVATE);
        $this->assertArrayHasKey('HIDDEN', $constants);
        $this->assertEquals('secret', $constants['HIDDEN']);
    }

    /**
     * @throws ReflectionException
     */
    public function testMethodsWithProtectedFilter()
    {
        $methods = $this->reflection->methods(MockUser::class, ReflectionMethod::IS_PROTECTED);
        $methodNames = array_map(fn($m) => $m->getName(), $methods);
        $this->assertContains('someProtectedMethod', $methodNames);
    }

    public function testShortNameWithInvalidClassThrows()
    {
        $this->expectException(ReflectionException::class);
        $this->reflection->shortName('NonExistentClass');
    }
}