<?php

namespace oihana\validations\rules;

use InvalidArgumentException;
use oihana\reflect\traits\ConstantsTrait;
use PHPUnit\Framework\TestCase;

use ReflectionClass;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

/**
 * Dummy class using ConstantsTrait for testing.
 */
final class ConstantsRuleDummyEnum
{
    use ConstantsTrait;

    public const string FOO = 'foo' ;
    public const string BAR = 'bar' ;
    public const string BAZ = 'baz' ;
}

/**
 * Dummy class NOT using ConstantsTrait.
 */
final class ConstantsRuleDummyNonEnum {}

final class ConstantsRuleTest extends TestCase
{
    public function testConstructorThrowsExceptionForNonEnumClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ConstantsRule(ConstantsRuleDummyNonEnum::class);
    }

    public function testCasesDefaultsToAllConstants(): void
    {
        $rule  = new ConstantsRule(ConstantsRuleDummyEnum::class ) ;
        $cases = $rule->parameter(ConstantsRule::CASES ) ;
        $this->assertEquals( ['bar', 'baz', 'foo'] , $cases ) ; // sorted by ConstantsTrait::enums()
    }

    public function testCasesCanBeOverridden(): void
    {
        $rule = new ConstantsRule(ConstantsRuleDummyEnum::class, ['foo', 'bar']);
        $cases = $rule->parameter(ConstantsRule::CASES);
        $this->assertEquals(['foo', 'bar'], $cases);
    }

    public function testClassNameSetterAndFluentInterface(): void
    {
        $rule = new ConstantsRule(ConstantsRuleDummyEnum::class)->className(ConstantsRuleDummyEnum::class);
        $this->assertEquals(ConstantsRuleDummyEnum::class, $rule->parameter(ConstantsRule::CLASS_NAME));
    }

    public function testCasesSetterFluentInterface(): void
    {
        $rule = new ConstantsRule(ConstantsRuleDummyEnum::class);
        $rule->cases(['foo']);
        $this->assertEquals(['foo'], $rule->parameter(ConstantsRule::CASES));
    }

    /**
     * @throws ParameterException
     */
    public function testCheckReturnsTrueForValidValue(): void
    {
        $rule = new ConstantsRule(ConstantsRuleDummyEnum::class);
        $this->assertTrue($rule->check('foo'));
    }

    /**
     * @throws ParameterException
     */
    public function testCheckReturnsFalseForInvalidValue(): void
    {
        $rule = new ConstantsRule(ConstantsRuleDummyEnum::class);
        $this->assertFalse($rule->check('invalid'));
    }

    public function testCheckThrowsParameterExceptionIfParamsMissing(): void
    {
        $rule = new ConstantsRule(ConstantsRuleDummyEnum::class);
        $reflection = new ReflectionClass($rule);
        $property = $reflection->getProperty('params');
        $property->setValue($rule, []); // empty params

        $this->expectException(ParameterException::class);
        $rule->check('foo');
    }
}