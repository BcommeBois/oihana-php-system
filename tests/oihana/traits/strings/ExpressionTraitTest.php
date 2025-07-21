<?php

namespace oihana\traits\strings;

use PHPUnit\Framework\TestCase;

class ExpressionTraitTest extends TestCase
{
    private object $trait;

    protected function setUp(): void
    {
        $this->trait = new class { use ExpressionTrait, BetweenTrait; };
    }

    public function testClean(): void
    {
        $input = ['foo', '', null, 'bar'];
        $expected = ['foo', 'bar'];
        $this->assertSame($expected, $this->trait->clean($input));
    }

    public function testCompile(): void
    {
        $this->assertSame('', $this->trait->compile(null));
        $this->assertSame('x', $this->trait->compile('x'));
        $this->assertSame('', $this->trait->compile(['', null]));
        $this->assertSame('a b', $this->trait->compile(['a', 'b']));
        $this->assertSame('a|b', $this->trait->compile(['a', 'b'], '|'));
    }

    public function testFunc(): void
    {
        $this->assertSame('SUM()', $this->trait->func('SUM'));
        $this->assertSame('SUM(a)', $this->trait->func('SUM', 'a'));
        $this->assertSame('SUM(a,b)', $this->trait->func('SUM', ['a', 'b']));
        $this->assertSame('SUM(a|b)', $this->trait->func('SUM', ['a', 'b'], '|'));
    }

    public function testKey(): void
    {
        $this->assertSame('name', $this->trait->key('name', null));
        $this->assertSame('name', $this->trait->key('name', ''));
        $this->assertSame('doc.name', $this->trait->key('name', 'doc'));
    }

    public function testKeyValue(): void
    {
        $this->assertSame('status: "active"', $this->trait->keyValue('status', '"active"'));
        $this->assertSame('x: 42', $this->trait->keyValue('x', 42));
    }

    public function testObject(): void
    {
        $this->assertSame('{}', $this->trait->object(null));
        $this->assertSame('{}', $this->trait->object(''));
        $this->assertSame('{ name: "Alice" }', $this->trait->object([['name', '"Alice"']]));
        $this->assertSame('{ name: "Alice", age: 30 }', $this->trait->object([['name', '"Alice"'], ['age', 30]]));
    }

    public function testPredicate(): void
    {
        $this->assertSame( 'x'      , $this->trait->predicate('x' ) );
        $this->assertSame( 'x == 1' , $this->trait->predicate('x' , '==', 1));
        $this->assertSame( 'y != '  , $this->trait->predicate('y' , '!=', null));
    }

    public function testPredicates(): void
    {
        $predicates =
        [
            $this->trait->predicate('a', '==', 1),
            $this->trait->predicate('b', '>', 0)
        ];
        $expected = '(a == 1 AND b > 0)';
        $this->assertSame($expected, $this->trait->predicates( $predicates , 'AND', true));

        $expected2 = 'a == 1 AND b > 0';
        $this->assertSame($expected2, $this->trait->predicates($predicates, 'AND', false));

        $this->assertNull($this->trait->predicates([], 'AND'));
        $this->assertNull($this->trait->predicates(null, 'OR'));
    }

    public function testWrap(): void
    {
        $this->assertSame('`column`'  , $this->trait->wrap('column'));
        $this->assertSame('`na\\`me`' , $this->trait->wrap('na`me'));
    }
}