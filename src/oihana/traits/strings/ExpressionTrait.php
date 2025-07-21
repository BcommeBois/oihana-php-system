<?php

namespace oihana\traits\strings;

use oihana\enums\Char;
use function oihana\core\arrays\isIndexed;

/**
 * A collection of utility methods for building and formatting string expressions
 * used in dynamic query generation or structured output (e.g., AQL, JSON-like formats).
 *
 * This trait provides:
 * - Cleaning utilities for expression arrays
 * - Syntax helpers for function calls, key-value pairs, and object-like structures
 * - Predicate and logical condition composition
 * - String wrapping utilities
 *
 * These methods are especially useful in libraries where query composition and
 * syntactic consistency are critical.
 *
 * Requires:
 * - The use of `Char` enum for consistent delimiters and symbols
 *
 * @package oihana\strings
 *
 * @example
 * ```php
 * $this->betweenParentheses(['a', 'b']); // '(a b)'
 * $this->betweenBraces("id: 1");         // '{ id: 1 }'
 * $this->betweenBrackets([1, 2, 3]);     // '[1 2 3]'
 * $this->betweenQuotes("hello");         / "'hello'"
 * $this->betweenDoubleQuotes("world");   / '"world"'
 * $this->betweenChars("x", "<", ">");    / '<x>'
 *
 * $this->compile(['x == 1', 'y == 2']);                // 'x == 1 y == 2'
 * $this->func('SUM', ['a', 'b']);                      // 'SUM(a,b)'
 * $this->key('name', 'user');                          // 'user.name'
 * $this->keyValue('status', '"active"');               // 'status: "active"'
 * $this->object([['id', 1], ['name', "'Alice'"]]);     // '{ id: 1, name: 'Alice' }'
 * $this->predicate('x', '==', 42);                     // 'x == 42'
 * $this->predicates(['a == 1', 'b > 0'], 'AND', true); // '(a == 1 AND b > 0)'
 * $this->wrap('field');                                // '`field`'
 * ```
 *
 * @package oihana\traits\strings
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait ExpressionTrait
{
    /**
     * Encapsulates an expression between specific characters.
     *
     * @param mixed $expression The expression to encapsulate between two characters.
     * @param string $left The left character.
     * @param string|null $right The right character. If null, uses the left character.
     * @param bool $flag Indicates whether to apply the wrapping.
     * @param string $separator The separator used to join arrays.
     * @return mixed The wrapped string or original expression.
     *
     * @example
     * ```php
     * $this->betweenChars('x', '<', '>');         // '<x>'
     * $this->betweenChars(['a', 'b'], '[', ']');  // '[a b]'
     * $this->betweenChars('y', '"', null, false); // 'y'
     * ```
     */
    public function betweenChars( mixed $expression = null , string $left = Char::EMPTY , ?string $right = null , bool $flag = true , string $separator = Char::SPACE ) :mixed
    {
        if( is_null( $right ) )
        {
            $right = $left ;
        }

        if( is_array( $expression ) )
        {
            $expression = $this->compile( $expression , $separator ) ;
        }

        if( is_null( $expression ) )
        {
            $expression = Char::EMPTY ;
        }

        return isset( $expression ) && $flag ? ( $left . $expression . $right ) : $expression ;
    }

    /**
     * Encapsulates an expression between braces (`{}`).
     *
     * @param mixed $expression The expression to wrap.
     * @param bool $useBraces Whether to apply the braces or not.
     * @param string $separator Separator for arrays (default: space).
     * @return string The wrapped string.
     *
     * @example
     * ```php
     * $this->betweenBraces('id: 1');           // '{ id: 1 }'
     * $this->betweenBraces(['x', 'y']);        // '{ x y }'
     * $this->betweenBraces(['x', 'y'], false); // 'x y'
     * ```
     */
    public function betweenBraces( mixed $expression = Char::EMPTY , bool $useBraces = true , string $separator = Char::SPACE ) :string
    {
        return $this->betweenChars( $expression , Char::LEFT_BRACE , Char::RIGHT_BRACE , $useBraces , $separator ) ;
    }

    /**
     * Encapsulates an expression between brackets (`[]`).
     *
     * @param mixed $expression The expression to wrap.
     * @param bool $useBrackets Whether to apply the brackets.
     * @param string $separator Separator for arrays.
     * @return string The wrapped string.
     *
     * @example
     * ```php
     * $this->betweenBrackets(['a', 'b']);     // '[a b]'
     * $this->betweenBrackets('index: 3');     // '[index: 3]'
     * $this->betweenBrackets('value', false); // 'value'
     * ```
     */
    public function betweenBrackets( mixed $expression = Char::EMPTY , bool $useBrackets = true , string $separator = Char::SPACE ) :string
    {
        return $this->betweenChars( $expression , Char::LEFT_BRACKET , Char::RIGHT_BRACKET , $useBrackets , $separator ) ;
    }

    /**
     * Encapsulates an expression between double quotes.
     *
     * @param mixed $expression The expression to wrap.
     * @param string $char The quote character (default: `"`).
     * @param bool $useQuotes Whether to apply quotes.
     * @param string $separator Separator for arrays.
     * @return mixed The wrapped string or original.
     *
     * @example
     * ```php
     * $this->betweenDoubleQuotes('hello');         // '"hello"'
     * $this->betweenDoubleQuotes(['a', 'b']);      // '"a b"'
     * $this->betweenDoubleQuotes('x', '"', false); // 'x'
     * ```
     */
    public function betweenDoubleQuotes( mixed $expression = Char::EMPTY , string $char = Char::DOUBLE_QUOTE , bool $useQuotes = true , string $separator = Char::SPACE ) :mixed
    {
        return $this->betweenChars( $expression , $char , $char , $useQuotes , $separator ) ;
    }

    /**
     * Encapsulates an expression between parentheses (`()`).
     *
     * @param mixed $expression The expression to wrap.
     * @param bool $useParentheses Whether to apply the parentheses.
     * @param string $separator Separator for arrays.
     * @return string The wrapped string.
     *
     * @example
     * ```php
     * $this->betweenParentheses('sum: 10');       // '(sum: 10)'
     * $this->betweenParentheses(['a', 'b', 'c']); // '(a b c)'
     * $this->betweenParentheses('val', false);    // 'val'
     * ```
     */
    public function betweenParentheses( mixed $expression = Char::EMPTY , bool $useParentheses = true , string $separator = Char::SPACE ) :string
    {
        return $this->betweenChars( $expression , Char::LEFT_PARENTHESIS , Char::RIGHT_PARENTHESIS , $useParentheses , $separator ) ;
    }

    /**
     * Encapsulates an expression between single quotes or custom characters.
     *
     * @param mixed $expression The expression to wrap.
     * @param string $char The quote character (default: `'`).
     * @param bool $useQuotes Whether to apply the quotes.
     * @param string $separator Separator for arrays.
     * @return mixed The wrapped string or original.
     *
     * @example
     * ```php
     * $this->betweenQuotes('world');           // '\'world\''
     * $this->betweenQuotes(['foo', 'bar']);    // '\'foo bar\''
     * $this->betweenQuotes('data', '`');       // '`data`'
     * $this->betweenQuotes('raw', "'", false); // 'raw'
     * ```
     */
    public function betweenQuotes( mixed $expression = Char::EMPTY , string $char = Char::SIMPLE_QUOTE , bool $useQuotes = true , string $separator = Char::SPACE ) :mixed
    {
        return $this->betweenChars( $expression , $char , $char , $useQuotes , $separator ) ;
    }

    /**
     * Clean an array by removing null values and empty strings.
     *
     * @param array $array The array to clean.
     * @return array The filtered array.
     *
     * @example
     * ```php
     * $this->clean(['foo', '', null, 'bar']); // ['foo', 'bar']
     * ```
     */
    public function clean( array $array = [] ):array
    {
        $indexed  = isIndexed( $array ) ;
        $filtered = array_filter( $array , fn( $value ) => !is_null( $value ) && $value !== Char::EMPTY ) ;
        return $indexed ? array_values( $filtered ) : $filtered;
    }

    /**
     * Compile a list of expressions into a single string using a separator.
     *
     * @param string|array|null $expressions The expressions to compile.
     * @param string $separator The separator to use (default: Char::SPACE).
     * @return string The compiled string.
     *
     * @example
     * ```php
     * $this->compile(['name = "foo"', 'age > 21']); // 'name = "foo" age > 21'
     * $this->compile(null);                         // ''
     * ```
     */
    public function compile( string|array|null $expressions , string $separator = Char::SPACE ) :string
    {
        if( is_array( $expressions ) && count( $expressions ) > 0 )
        {
            $expressions = $this->clean( $expressions ) ;
            return count( $expressions ) > 0 ? implode( $separator , $expressions ) : Char::EMPTY ;
        }
        return is_string( $expressions ) ? $expressions : Char::EMPTY ;
    }

    /**
     * Build a function expression like `NAME(arg1,arg2)`.
     *
     * @param string $name The function name.
     * @param mixed $arguments The arguments for the function.
     * @param string $separator The separator between arguments (default: Char::COMMA).
     * @return string The function expression.
     *
     * @example
     * ```php
     * $this->func('CALL', ['a', 'b']); // 'CALL(a,b)'
     * ```
     */
    public function func( string $name , mixed $arguments = null , string $separator = Char::COMMA ) :string
    {
        return $name . $this->betweenParentheses( $arguments , true , $separator ) ; 
    }

    /**
     * Transform a key by optionally prefixing it.
     *
     * @param string $key The key to transform.
     * @param string|null $prefix The prefix to prepend.
     * @return string The transformed key.
     *
     * @example
     * ```php
     * $this->key('name');           // 'name'
     * $this->key('name', 'doc');    // 'doc.name'
     * ```
     */
    public function key( string $key , ?string $prefix ):string
    {
        return is_string( $prefix ) && $prefix !== Char::EMPTY ? $prefix . Char::DOT . $key : $key ;
    }

    /**
     * Build a key-value expression like `key: value`.
     *
     * @param mixed $key The key.
     * @param mixed $value The value.
     * @return string The key-value expression.
     *
     * @example
     * ```php
     * $this->keyValue('name', 'Eka'); // 'name: Eka'
     * ```
     */
    public function keyValue( string $key , mixed $value ):string
    {
        return $key . Char::COLON . Char::SPACE . $value ;
    }

    /**
     * Create an object expression, e.g., `{ name: 'Eka', age: 48 }`.
     *
     * @param string|array|null $keyValues The properties to include.
     * @return string The object-like string expression.
     *
     * @example
     * ```php
     * $this->object([['name', "'Eka'"], ['age', 47]]);
     * // '{ name: 'Eka', age: 47 }'
     * ```
     */
    public function object( null|string|array $keyValues = [] ):string
    {
        if( is_array( $keyValues ) )
        {
            $properties = array_map( fn( $keyValue ) => is_array( $keyValue ) ? $this->keyValue( $keyValue[0] , $keyValue[1] ) : $keyValue , $keyValues ) ;
            $keyValues  = implode( Char::COMMA . Char::SPACE , $properties ) ;
        }
        elseif( is_null( $keyValues ) )
        {
            $keyValues = Char::EMPTY ;
        }
        return $this->betweenBraces( $keyValues != Char::EMPTY ? Char::SPACE . $keyValues . Char::SPACE : Char::EMPTY  ) ;
    }

    /**
     * Generate a predicate expression.
     *
     * @param mixed $leftOperand The left-hand value.
     * @param string|null $operator The operator (e.g., '==', '!=').
     * @param mixed $rightOperand The right-hand value.
     * @return string The predicate expression.
     *
     * @example
     * ```php
     * $this->predicate('age', '>=', 18); // 'age >= 18'
     * ```
     */
    public function predicate( mixed $leftOperand , ?string $operator = null , mixed $rightOperand = null ) :string
    {
        $expression = [ $leftOperand ] ;
        if( !is_null( $operator ) )
        {
            $expression[] = $operator ;
            $expression[] = is_null( $rightOperand ) ? null : $rightOperand ;
        }
        return implode( Char::SPACE , $expression ) ;
    }

    /**
     * Generate a complex logical expression with multiple predicates.
     *
     * @param array|null $conditions List of predicate expressions.
     * @param string $logicalOperator The logical operator to join predicates (e.g., 'AND', 'OR').
     * @param bool $useParentheses Whether to wrap the result in parentheses.
     * @return string|null The combined expression or null if empty.
     *
     * @example
     * ```php
     * $predicates = [
     *     $this->predicate('a', '==', 1),
     *     $this->predicate('b', '>', 5)
     * ];
     * $this->predicates( $predicates , 'AND' , true ) ; // '(a == 1 AND b > 5)'
     * ```
     */
    public function predicates( ?array $conditions , string $logicalOperator , bool $useParentheses = false ) :?string
    {
        if( is_array( $conditions ) )
        {
            $conditions = $this->clean( $conditions ) ;
            if( count( $conditions ) > 0 )
            {
                return $this->betweenParentheses(  $conditions , $useParentheses , Char::SPACE . $logicalOperator . Char::SPACE ) ;
            }
        }
        return null ;
    }

    /**
     * Wrap a string in grave accent characters.
     *
     * @param string $value The value to wrap.
     * @return string The wrapped value.
     *
     * @example
     * ```php
     * $this->wrap('column'); // '`column`'
     * ```
     */
    public function wrap( string $value ): string
    {
        return $this->betweenChars( addcslashes( $value , Char::GRAVE_ACCENT ) , Char::GRAVE_ACCENT ) ;
    }
}