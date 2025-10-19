<?php

namespace tests\oihana\validations\rules;

use PHPUnit\Framework\TestCase;

use oihana\validations\rules\ColorRule;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class ColorRuleTest extends TestCase
{
    /**
     * @throws ParameterException
     */
    public function testDefaultValidationPasses(): void
    {
        $rule = new ColorRule();
        $this->assertTrue($rule->check('#ff00ff'));
        $this->assertTrue($rule->check('#A1B2C3'));
    }

    /**
     * @throws ParameterException
     */
    public function testValidationFailsForInvalidColor(): void
    {
        $rule = new ColorRule();
        $this->assertFalse($rule->check('ff00ff'));
        $this->assertFalse($rule->check('#GGGGGG'));
        $this->assertFalse($rule->check('#123'));
    }

    /**
     * @throws ParameterException
     */
    public function testCustomPrefixWithoutHash(): void
    {
        $rule = new ColorRule(['prefix' => '']);
        $this->assertTrue($rule->check('ff00ff'));
        $this->assertFalse($rule->check('#ff00ff'));
    }

    /**
     * @throws ParameterException
     */
    public function testCustomPatternUppercaseOnly(): void
    {
        $rule = new ColorRule(['pattern' => '/^%s[A-F0-9]{6}$/', 'prefix' => '#']);
        $this->assertTrue($rule->check('#ABCDEF'));
        $this->assertFalse($rule->check('#abc123'));
    }

    /**
     * @throws ParameterException
     */
    public function testReturnsFalseForNonStringValues(): void
    {
        $rule = new ColorRule();
        $this->assertFalse($rule->check(123456));
        $this->assertFalse($rule->check(['#ff0000']));
        $this->assertFalse($rule->check(null));
    }
}