<?php

namespace tests\oihana\validations\rules;

use DateTime;
use PHPUnit\Framework\TestCase;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

use oihana\validations\rules\InstanceOfRule;
use stdClass;

final class InstanceOfRuleTest extends TestCase
{
    public function testConstructorSetsClassName(): void
    {
        $rule = new InstanceOfRule( DateTime::class);
        $this->assertSame( DateTime::class, $rule->parameter(InstanceOfRule::CLASS_NAME));
    }

    public function testClassNameSetterReturnsSelf(): void
    {
        $rule = new InstanceOfRule();
        $this->assertSame($rule, $rule->className( stdClass::class));
    }

    /**
     * @throws ParameterException
     */
    public function testPassesWhenValueIsInstanceOf(): void
    {
        $rule = new InstanceOfRule( DateTime::class);
        $this->assertTrue($rule->check(new DateTime()));
    }

    /**
     * @throws ParameterException
     */
    public function testFailsWhenValueIsNotInstanceOf(): void
    {
        $rule = new InstanceOfRule( DateTime::class);
        $this->assertFalse($rule->check(new stdClass()));
    }

    /**
     * @throws ParameterException
     */
    public function testFailsWhenClassDoesNotExist(): void
    {
        $rule = new InstanceOfRule('NonExistingClass');
        $this->assertFalse($rule->check(new stdClass()));
    }

    /**
     * @throws ParameterException
     */
    public function testFailsWhenValueIsNotAnObject(): void
    {
        $rule = new InstanceOfRule( DateTime::class);
        $this->assertFalse($rule->check('string'));
        $this->assertFalse($rule->check(123));
        $this->assertFalse($rule->check(null));
    }
}