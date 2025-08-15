<?php

namespace tests\oihana\init;

use oihana\enums\IniOptions;
use oihana\reflect\exceptions\ConstantException;
use PHPUnit\Framework\TestCase;
use function oihana\init\setIniIfExists;

/**
 * Unit tests for setIniIfExists()
 *
 * Notes:
 * - We use the display_errors directive because it is safe to modify at runtime in CLI.
 * - Each test restores the original value in tearDown to avoid side effects.
 */
class SetIniIfExistsTest extends TestCase
{
    private string $key = IniOptions::DISPLAY_ERRORS;
    private string $original = '';

    protected function setUp(): void
    {
        if (!function_exists('ini_set'))
        {
            $this->markTestSkipped('ini_set() is not available in this environment.');
        }
        $val = ini_get($this->key);
        $this->original = is_string($val) ? $val : (string)$val;
    }

    protected function tearDown(): void
    {
        if (function_exists('ini_set')) {
            ini_set($this->key, $this->original);
        }
    }

    /**
     * @throws ConstantException
     */
    public function testSetsIniFromScalarValue(): void
    {
        $current = ini_get($this->key);
        $current = is_string($current) ? $current : (string)$current;
        $new = ($current === '1') ? '0' : '1';

        $called = setIniIfExists($this->key, $new);

        $this->assertTrue($called, 'Expected setIniIfExists to call ini_set for scalar value');
        $this->assertSame($new, (string)ini_get($this->key), 'display_errors should reflect the new value');
    }

    /**
     * @throws ConstantException
     */
    public function testSetsIniFromArrayWhenKeyPresent(): void
    {
        $current = ini_get($this->key);
        $current = is_string($current) ? $current : (string)$current;
        $new = ($current === '1') ? '0' : '1';

        $config = [ $this->key => $new ];
        $called = setIniIfExists($this->key, $config);

        $this->assertTrue($called, 'Expected setIniIfExists to call ini_set when key exists in array');
        $this->assertSame($new, (string)ini_get($this->key));
    }

    /**
     * @throws ConstantException
     */
    public function testReturnsFalseWhenKeyMissingInArray(): void
    {
        $before = (string) ini_get($this->key);

        $called = setIniIfExists( $this->key ); // key not present

        $this->assertFalse($called, 'Expected setIniIfExists to return false when key is missing in array');
        $this->assertSame( $before , (string) ini_get($this->key) , 'Ini value must remain unchanged');
    }

    /**
     * @throws ConstantException
     */
    public function testReturnsFalseWhenValueIsEmptyOrWhitespace(): void
    {
        $before = (string)ini_get($this->key);

        $called = setIniIfExists($this->key, " \t\n");

        $this->assertFalse($called, 'Expected setIniIfExists to return false for empty/whitespace value');
        $this->assertSame($before, (string)ini_get($this->key), 'Ini value must remain unchanged');
    }
}
