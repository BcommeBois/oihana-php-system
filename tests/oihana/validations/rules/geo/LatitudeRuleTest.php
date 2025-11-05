<?php

namespace tests\oihana\validations\rules\geo;

use oihana\validations\rules\geo\LatitudeRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LatitudeRuleTest extends TestCase
{
    public static function latitudeProvider(): iterable
    {
        yield [0, true];
        yield [45, true];
        yield [-45, true];
        yield [90, true];
        yield [-90, true];
        yield [90.0001, false];
        yield [-90.1, false];
        yield ['89.99', true];
        yield ['-91', false];
        yield ['foo', false];
        yield [null, false];
        yield ['', false];
    }

    #[DataProvider('latitudeProvider')]
    public function testLatitudeValidation(mixed $value, bool $expected): void
    {
        $rule = new LatitudeRule();

        $this->assertSame(
            $expected,
            $rule->check($value),
            sprintf('Failed asserting that %s is %svalid latitude.', var_export($value, true), $expected ? '' : 'not ')
        );
    }
}