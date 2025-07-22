<?php

namespace oihana\abstracts;

use InvalidArgumentException;
use ReflectionException;

use PHPUnit\Framework\TestCase;

use oihana\abstracts\mocks\MockOptions ;

class OptionsTest extends TestCase
{
    public function testConstructorInitializesProperties(): void
    {
        $options = new MockOptions
        ([
            'name'  => 'backup',
            'count' => 3,
            'tags'  => ['dev', 'test'],
            'force' => true,
        ]);

        $this->assertSame('backup', $options->name);
        $this->assertSame(3, $options->count);
        $this->assertSame(['dev', 'test'], $options->tags);
        $this->assertTrue($options->force);
    }

    public function testCreateWithArray(): void
    {
        $options = MockOptions::create([
            'name'  => 'sync',
            'count' => 5,
        ]);

        $this->assertSame('sync', $options->name);
        $this->assertSame(5, $options->count);
    }

    public function testCreateWithInstance(): void
    {
        $original = new MockOptions(['name' => 'import']);
        $copy = MockOptions::create($original);

        $this->assertSame($original, $copy);
    }

    public function testCreateWithNull(): void
    {
        $options = MockOptions::create();

        $this->assertSame('', $options->name);
    }

    public function testToStringGeneratesExpectedOutput(): void
    {
        $options = new MockOptions([
            'name'  => 'run',
            'count' => 2,
            'tags'  => ['prod', 'logs'],
            'force' => true,
        ]);

        $expected = '--name "run" --count 2 --tag "prod" --tag "logs" --force';
        $this->assertSame($expected, (string)$options);
    }

    /**
     * @throws ReflectionException
     */
    public function testGetOptionsThrowsOnInvalidClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/must implement the Optionable interface/');

        $class = new class {
            public string $dummy = 'test';
        };

        // simulate a call to getOptions() with a non-Optionable class
        $options = new MockOptions();
        /** @noinspection PhpParamsInspection */
        $options->getOptions($class::class);
    }
}