<?php

namespace oihana\abstracts;

use oihana\abstracts\mocks\MockOption;
use oihana\abstracts\mocks\MockOptions;
use PHPUnit\Framework\TestCase;
use ReflectionException;

class OptionsTest extends TestCase
{
    public function getConcreteOptionsInstance(array|object|null $init = null): Options
    {
        return new MockOptions( $init ) ;
    }

    public function testCreateWithArrayReturnsInstance()
    {
        $arr = ['foo' => 'val'];
        $instance = MockOptions::create($arr);
        $this->assertSame('val', $instance->foo);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetOptionsReturnsCorrectString()
    {
        $options = $this->getConcreteOptionsInstance([
            'foo' => 'value',
            'bar' => true,
            'baz' => ['item1', 'item2'],
        ]);

        $result = $options->getOptions(MockOption::class );

        $this->assertStringContainsString('--foo "value"', $result);
        $this->assertStringContainsString('--bar', $result);
        $this->assertStringContainsString('--baz "item1"', $result);
        $this->assertStringContainsString('--baz "item2"', $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetOptionsWithPrefix()
    {
        $options = $this->getConcreteOptionsInstance([
            'foo' => 'value',
            'bar' => true,
        ]);

        $result = $options->getOptions(MockOption::class, 'PREFIX_');

        $this->assertStringContainsString('PREFIX_--foo "value"', $result);
        $this->assertStringContainsString('PREFIX_--bar', $result);
    }
}