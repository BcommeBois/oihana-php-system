<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\RangeRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class RangeRuleTest extends TestCase
{
    public static function rangeProvider(): iterable
    {
        yield [0, 100, 50, true];       // valeur à l’intérieur
        yield [0, 100, 0, true];        // limite min
        yield [0, 100, 100, true];      // limite max
        yield [0, 100, -1, false];      // inférieur à min
        yield [0, 100, 101, false];     // supérieur à max
        yield ['-10', '10', 5, true];   // min/max sous forme string
        yield ['-10', '10', 'foo', false]; // valeur non numérique
        yield ['-10', '10', null, false];  // null
    }

    /**
     * @throws ParameterException
     */
    #[DataProvider('rangeProvider')]
    public function testRangeRule(mixed $min, mixed $max, mixed $value, bool $expected): void
    {
        $rule = new RangeRule();
        $rule->fillParameters(['min' => $min, 'max' => $max]);

        $this->assertSame(
            $expected,
            $rule->check($value),
            sprintf('RangeRule failed for %s ≤ %s ≤ %s', var_export($min, true), var_export($value, true), var_export($max, true))
        );
    }

    public function testThrowsIfParametersMissing(): void
    {
        $rule = new RangeRule();

        $this->expectException(ParameterException::class);
        $rule->check(10);
    }

    public function testThrowsIfParametersAreNotNumeric(): void
    {
        $rule = new RangeRule();
        $rule->fillParameters(['min' => 'foo', 'max' => 'bar']);

        $this->expectException(ParameterException::class);
        $rule->check(10);
    }

    /**
     * @throws ParameterException
     */
    public function testWorksWithNegativeValues(): void
    {
        $rule = new RangeRule();
        $rule->fillParameters(['min' => -50, 'max' => -10]);

        $this->assertTrue($rule->check(-20));
        $this->assertFalse($rule->check(-5));
        $this->assertFalse($rule->check(-60));
    }

    public function testWorksWithFloatValues(): void
    {
        $rule = new RangeRule();
        $rule->fillParameters(['min' => 0.5, 'max' => 1.5]);

        $this->assertTrue($rule->check(1.0));
        $this->assertFalse($rule->check(0.4));
        $this->assertFalse($rule->check(1.6));
    }
}