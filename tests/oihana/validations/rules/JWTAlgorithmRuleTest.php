<?php

namespace tests\oihana\validations\rules;

use ReflectionClass;
use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\Validator;

use oihana\validations\rules\JWTAlgorithmRule;

use xyz\oihana\schema\constants\JWTAlgorithm;

use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class JWTAlgorithmRuleTest extends TestCase
{
    /**
     * Ensures that the rule initializes with all algorithms by default.
     */
    public function testDefaultCasesUseAllJWTAlgorithms(): void
    {
        $rule = new JWTAlgorithmRule();

        $this->assertSame
        (
            JWTAlgorithm::enums(),
            $rule->parameter(JWTAlgorithmRule::CASES),
            'The default case list should match JWTAlgorithm::enums()'
        );
    }

    /**
     * Ensures that the rule accepts a custom subset of algorithms.
     */
    public function testCustomCasesCanBeProvided(): void
    {
        $custom = ['HS256', 'RS256'];

        $rule = new JWTAlgorithmRule($custom);

        $this->assertSame(
            $custom,
            $rule->parameter(JWTAlgorithmRule::CASES),
            'The custom algorithm list should override the default one.'
        );
    }

    /**
     * Verifies that valid algorithm names pass validation.
     * @throws ParameterException
     */
    public function testValidAlgorithmPasses(): void
    {
        $rule = new JWTAlgorithmRule();

        foreach (JWTAlgorithm::enums() as $alg)
        {
            $this->assertTrue(
                $rule->check($alg),
                "Expected algorithm '{$alg}' to be valid."
            );
        }
    }

    /**
     * Verifies that invalid algorithm names fail validation.
     */
    public function testInvalidAlgorithmFails(): void
    {
        $rule = new JWTAlgorithmRule();

        $invalid = ['MD5', 'SHA1', 'HMAC', 'PS1024', ''];

        foreach ($invalid as $alg) {
            $this->assertFalse(
                $rule->check($alg),
                "Expected algorithm '{$alg}' to be invalid."
            );
        }
    }

    /**
     * Ensures that the rule respects case sensitivity.
     */
    public function testCaseSensitivity(): void
    {
        $rule = new JWTAlgorithmRule();

        $this->assertFalse(
            $rule->check('hs256'),
            'Algorithm names must be case-sensitive (HS256 != hs256).'
        );
    }

    /**
     * Ensures that the rule works when restricted to a subset.
     */
    public function testSubsetValidation(): void
    {
        $rule = new JWTAlgorithmRule(['HS256', 'RS256']);

        $this->assertTrue($rule->check('HS256'));
        $this->assertTrue($rule->check('RS256'));
        $this->assertFalse($rule->check('RS512'));
    }

    /**
     * Ensures that the default error message is correctly set.
     */
    public function testDefaultErrorMessage(): void
    {
        $rule = new JWTAlgorithmRule();
        $ref = new ReflectionClass($rule);

        $prop = $ref->getProperty('message');

        $this->assertSame(
            ':attribute is not a valid JWT signing algorithm.',
            $prop->getValue($rule)
        );
    }
}