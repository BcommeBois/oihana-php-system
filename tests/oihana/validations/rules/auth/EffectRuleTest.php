<?php

namespace tests\oihana\validations\rules\auth;

use oihana\validations\rules\auth\EffectRule;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Somnambulist\Components\Validation\Exceptions\ParameterException;
use xyz\oihana\schema\constants\Effect;

final class EffectRuleTest extends TestCase
{
    /**
     * @throws ParameterException
     */
    public function testValidEffectPasses(): void
    {
        $rule = new EffectRule();
        foreach (Effect::enums() as $effect)
        {
            $this->assertTrue( $rule->check($effect), "Expected algorithm '$effect' to be valid." );
        }
    }

    /**
     * @throws ParameterException
     */
    public function testInvalidEffectFails(): void
    {
        $rule = new EffectRule();

        $invalid = [ 2, 'unknow' , true ];

        foreach ($invalid as $effect)
        {
            $this->assertFalse
            (
                $rule->check( $effect ) ,
                "Expected algorithm '$effect' to be invalid."
            );
        }
    }

    /**
     * Ensures that the rule respects case sensitivity.
     * @throws ParameterException
     */
    public function testCaseSensitivity(): void
    {
        $rule = new EffectRule();

        $this->assertFalse(
            $rule->check('ALLOW'),
            'Algorithm names must be case-sensitive (allow != ALLOW).'
        );
    }

    /**
     * Ensures that the default error message is correctly set.
     */
    public function testDefaultErrorMessage(): void
    {
        $rule = new EffectRule();
        $ref  = new ReflectionClass($rule);
        $prop = $ref->getProperty('message');

        $this->assertSame(
            ":attribute is not a valid. Allowed values are 'allow' or 'deny'.",
            $prop->getValue($rule)
        );
    }
}