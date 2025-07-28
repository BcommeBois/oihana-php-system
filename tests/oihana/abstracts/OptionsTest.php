<?php

namespace oihana\abstracts;

use InvalidArgumentException;
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

    public function testCreateWithInstanceReturnsSame()
    {
        $original = new MockOptions(['foo' => 'bar']);
        $created = MockOptions::create($original);
        $this->assertSame($original, $created);
    }

    public function testCloneReturnsDeepCopy()
    {
        $original = new MockOptions(['foo' => 'bar']);
        $cloned = $original->clone();

        $this->assertNotSame($original, $cloned);
        $this->assertEquals($original, $cloned);
    }

    public function testFormatReplacesPlaceholders()
    {
        $options = $this->getConcreteOptionsInstance
        ([
            'foo' => 'hello',
            'bar' => true,
        ]);

        $template = 'Prefix: {{foo}}, Flag: {{bar}}, Missing: {{baz}}';
        $result = $options->format($template);

        $this->assertSame('Prefix: hello, Flag: 1, Missing: []', $result);
    }

    public function testFormatWithCustomDelimiters()
    {
        $options = $this->getConcreteOptionsInstance(['foo' => 'abc']);
        $template = 'Value is <<foo>>';
        $result = $options->format($template, '<<', '>>');

        $this->assertSame('Value is abc', $result);
    }

    public function testFormatReturnsNullForInvalidTemplate()
    {
        $options = $this->getConcreteOptionsInstance();
        $this->assertNull($options->format());
        $this->assertNull($options->format(''));
    }

    public function testFormatArrayRecursivelyFormatsValues()
    {
        $options = $this->getConcreteOptionsInstance
        ([
            'domain'    => 'domain.com',
            'subdomain' => 'www'
        ]);

        $array = [
            'url' => 'https://{{subdomain}}.{{domain}}',
            'nested' => [
                'api' => 'https://api.{{domain}}/v1'
            ],
            'unchanged' => 123
        ];

        $expected = [
            'url' => 'https://www.domain.com',
            'nested' => [
                'api' => 'https://api.domain.com/v1'
            ],
            'unchanged' => 123
        ];

        $result = $options->formatArray($array);
        $this->assertSame($expected, $result);
    }

    public function testToStringReturnsCustomValue()
    {
        $options = new MockOptions();
        $this->assertSame('OptionsToString', (string) $options);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetOptionsReturnsCorrectString()
    {
        $options = $this->getConcreteOptionsInstance
        ([
            'foo' => 'value',
            'bar' => true,
            'baz' => ['item1', 'item2'],
        ]);

        $result = $options->getOptions(MockOption::class);

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
        $options = $this->getConcreteOptionsInstance
        ([
            'foo' => 'value',
            'bar' => true,
        ]);

        $result = $options->getOptions(MockOption::class, 'PREFIX_');

        $this->assertStringContainsString('PREFIX_--foo "value"', $result);
        $this->assertStringContainsString('PREFIX_--bar', $result);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetOptionsThrowsExceptionForInvalidClass()
    {
        $options = $this->getConcreteOptionsInstance(['foo' => 'val']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/must inherit the Option class/');
        $options->getOptions(\stdClass::class);
    }
}