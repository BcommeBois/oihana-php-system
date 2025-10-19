<?php

namespace tests\oihana\validations\rules;

use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Exceptions\ParameterException;


use oihana\validations\rules\StartsWithRule;

final class StartsWithRuleTest extends TestCase
{
    public function testRuleNameConstant(): void
    {
        $this->assertSame('startsWith', StartsWithRule::NAME);
    }

    public function testConstructorSetsPrefix(): void
    {
        $rule = new StartsWithRule('foo');
        $this->assertSame('foo', $rule->parameter(StartsWithRule::PREFIX));
    }

    public function testPrefixSetterReturnsSelf(): void
    {
        $rule = new StartsWithRule();
        $this->assertSame($rule, $rule->prefix('bar'));
        $this->assertSame('bar', $rule->parameter(StartsWithRule::PREFIX));
    }

    /**
     * @throws ParameterException
     */
    public function testPassesWhenValueStartsWithPrefix(): void
    {
        $rule = new StartsWithRule('abc');
        $this->assertTrue($rule->check('abcdef'));
    }

    /**
     * @throws ParameterException
     */
    public function testPassesWhenValueEqualsPrefix(): void
    {
        $rule = new StartsWithRule('hello');
        $this->assertTrue($rule->check('hello'));
    }

    /**
     * @throws ParameterException
     */
    public function testPassesWhenPrefixIsEmpty(): void
    {
        $rule = new StartsWithRule('');
        $this->assertTrue($rule->check('anything'));
    }

    /**
     * @throws ParameterException
     */
    public function testFailsWhenValueDoesNotStartWithPrefix(): void
    {
        $rule = new StartsWithRule('abc');
        $this->assertFalse($rule->check('xyzabc'));
    }

    /**
     * @throws ParameterException
     */
    public function testFailsWithNonStringValue(): void
    {
        $rule = new StartsWithRule('foo');
        $this->assertFalse($rule->check(12345));
    }

    /**
     * @throws ParameterException
     */
    public function testHandlesStringableObject(): void
    {
        $stringable = new class {
            public function __toString(): string
            {
                return 'foobar';
            }
        };

        $rule = new StartsWithRule('foo');
        $this->assertTrue($rule->check($stringable));
    }
}