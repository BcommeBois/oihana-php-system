<?php

namespace tests\oihana\validations\rules\geo;

use oihana\validations\rules\geo\LongitudeRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LongitudeRuleTest extends TestCase
{
    public static function longitudeProvider(): iterable
    {
        yield [  180     , true  ] ;
        yield [  -181    , false ] ;
        yield [  '120.5' , true  ] ;
        yield [  'foo'   , false ] ;
        yield [  null    , false ] ;
    }

    #[DataProvider('longitudeProvider')]
    public function testLongitudeValidation(mixed $value, bool $expected): void
    {
        $rule = new LongitudeRule();

        $this->assertSame(
            $expected,
            $rule->check($value),
            sprintf('Failed asserting that %s is %svalid longitude.', var_export($value, true), $expected ? '' : 'not ')
        );
    }
}