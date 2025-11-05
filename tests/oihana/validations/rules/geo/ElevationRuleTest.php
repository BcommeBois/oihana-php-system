<?php

namespace tests\oihana\validations\rules\geo;

use oihana\validations\rules\geo\ElevationRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ElevationRuleTest extends TestCase
{
    public static function elevationProvider(): iterable
    {
        yield [0, true];          // sea level
        yield [8848, true];       // Everest
        yield [-10994, true];     // Mariana Trench
        yield [8900, true];       // upper limit
        yield [-11500, true];     // lower limit
        yield [9000, false];      // too high
        yield [-12000, false];    // too deep
        yield ['500', true];      // numeric string
        yield ['foo', false];     // invalid
        yield [null, false];      // null
        yield ['', false];        // empty string
    }

    #[DataProvider('elevationProvider')]
    public function testElevationValidation(mixed $value, bool $expected): void
    {
        $rule = new ElevationRule();

        $this->assertSame(
            $expected,
            $rule->check($value),
            sprintf('Failed asserting that %s is %svalid elevation.', var_export($value, true), $expected ? '' : 'not ')
        );
    }
}