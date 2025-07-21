<?php

namespace oihana\traits\strings;

use PHPUnit\Framework\TestCase;

class BetweenTraitTest extends TestCase
{
    use ExpressionTrait;

    public function testBetweenCharsWithSimpleString(): void
    {
        $this->assertSame('<x>', $this->betweenChars('x', '<', '>'));
    }

    public function testBetweenCharsWithArray(): void
    {
        $this->assertSame('[a b]', $this->betweenChars(['a', 'b'], '[', ']'));
    }

    public function testBetweenCharsWithDisabledWrapping(): void
    {
        $this->assertSame('value', $this->betweenChars('value', '[', ']', false));
    }

    public function testBetweenBraces(): void
    {
        $this->assertSame('{a: 1}', $this->betweenBraces('a: 1'));
    }

    public function testBetweenBracesWithArray(): void
    {
        $this->assertSame('{a b}', $this->betweenBraces(['a','b']));
    }

    public function testBetweenBracesDisabled(): void
    {
        $this->assertSame('x', $this->betweenBraces('x', false));
    }

    public function testBetweenBrackets(): void
    {
        $this->assertSame('[item]', $this->betweenBrackets('item'));
    }

    public function testBetweenBracketsWithArray(): void
    {
        $this->assertSame('[1 2 3]', $this->betweenBrackets([1, 2, 3]));
    }

    public function testBetweenBracketsDisabled(): void
    {
        $this->assertSame('raw', $this->betweenBrackets('raw', false));
    }

    public function testBetweenDoubleQuotes(): void
    {
        $this->assertSame('"hello"', $this->betweenDoubleQuotes('hello'));
    }

    public function testBetweenDoubleQuotesWithArray(): void
    {
        $this->assertSame('"a b"', $this->betweenDoubleQuotes(['a', 'b']));
    }

    public function testBetweenDoubleQuotesDisabled(): void
    {
        $this->assertSame('x', $this->betweenDoubleQuotes('x', '"', false));
    }

    public function testBetweenParentheses(): void
    {
        $this->assertSame('(42)', $this->betweenParentheses(42));
    }

    public function testBetweenParenthesesWithArray(): void
    {
        $this->assertSame('(a b c)', $this->betweenParentheses(['a', 'b', 'c']));
    }

    public function testBetweenParenthesesDisabled(): void
    {
        $this->assertSame('sum', $this->betweenParentheses('sum', false));
    }

    public function testBetweenQuotes(): void
    {
        $this->assertSame("'world'", $this->betweenQuotes('world'));
    }

    public function testBetweenQuotesWithCustomChar(): void
    {
        $this->assertSame('`foo`', $this->betweenQuotes('foo', '`'));
    }

    public function testBetweenQuotesWithArray(): void
    {
        $this->assertSame("'bar baz'", $this->betweenQuotes(['bar', 'baz']));
    }

    public function testBetweenQuotesDisabled(): void
    {
        $this->assertSame('test', $this->betweenQuotes('test', "'", false));
    }
}