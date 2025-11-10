<?php

namespace tests\oihana\validations\rules\http;

use oihana\validations\rules\ConstantsRule;
use oihana\validations\rules\http\HttpMethodRule;
use oihana\enums\http\HttpMethod;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class HttpMethodRuleTest extends TestCase
{
    /**
     * Ensures that the rule initializes with all HTTP methods by default.
     */
    public function testDefaultCasesUseAllHttpMethods(): void
    {
        $rule = new HttpMethodRule();

        $this->assertSame
        (
            HttpMethod::enums(),
            $rule->parameter(ConstantsRule::CASES),
            'The default case list should match HttpMethod::enums()'
        );
    }

    /**
     * Ensures that the rule accepts a custom subset of HTTP methods.
     */
    public function testCustomCasesCanBeProvided(): void
    {
        $custom = ['GET', 'POST', 'DELETE'];

        $rule = new HttpMethodRule($custom);

        $this->assertSame(
            $custom,
            $rule->parameter(ConstantsRule::CASES),
            'The custom HTTP method list should override the default one.'
        );
    }

    /**
     * Verifies that valid HTTP methods pass validation.
     * @throws ParameterException
     */
    public function testValidMethodsPass(): void
    {
        $rule = new HttpMethodRule();

        foreach ( HttpMethod::enums() as $method )
        {
            $this->assertTrue
            (
                $rule->check( $method ) ,
                "Expected method '$method' to be valid."
            );
        }
    }

    /**
     * Verifies that invalid HTTP methods fail validation.
     * @throws ParameterException
     */
    public function testInvalidMethodsFail(): void
    {
        $rule = new HttpMethodRule();

        $invalid = ['FOO', 'BAR', 'PATCHED', 'GETS', ''];

        foreach ($invalid as $method) {
            $this->assertFalse(
                $rule->check($method),
                "Expected method '$method' to be invalid."
            );
        }
    }

    /**
     * Ensures that the rule respects case sensitivity.
     * @throws ParameterException
     */
    public function testCaseSensitivity(): void
    {
        $rule = new HttpMethodRule();

        // Uppercase valid method should pass
        $this->assertTrue($rule->check('GET'));
        // Lowercase variant should also pass if included in enums
        $this->assertTrue($rule->check('get'));
        // Mixed invalid case
        $this->assertFalse($rule->check('gEt'));
    }

    /**
     * Ensures that the rule works when restricted to a subset.
     * @throws ParameterException
     */
    public function testSubsetValidation(): void
    {
        $rule = new HttpMethodRule(['GET', 'POST', 'DELETE']);

        $this->assertTrue($rule->check('GET'));
        $this->assertTrue($rule->check('POST'));
        $this->assertFalse($rule->check('PATCH'));
    }

    /**
     * Ensures that the default error message is correctly set.
     */
    public function testDefaultErrorMessage(): void
    {
        $rule = new HttpMethodRule();
        $ref = new ReflectionClass($rule);
        $prop = $ref->getProperty('message');

        $this->assertSame(
            ':attribute is not a valid HTTP method.',
            $prop->getValue($rule)
        );
    }
}