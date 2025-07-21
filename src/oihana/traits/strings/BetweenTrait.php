<?php

namespace oihana\traits\strings;

use oihana\enums\Char;
use oihana\exceptions\ExceptionTrait;

/**
 * Provides utility methods to wrap or encapsulate expressions between various types of characters,
 * such as parentheses, braces, brackets, and quotes.
 *
 * This trait is designed to assist in formatting string expressions for use in query builders,
 * DSLs, and any syntax-sensitive string generation context (e.g., AQL, JSON-like formats, etc.).
 *
 * All methods accept single expressions or arrays, which will be compiled and wrapped consistently.
 *
 * Common use cases include:
 * - Wrapping lists in parentheses for function arguments
 * - Formatting key-value sets inside JSON-style braces
 * - Surrounding strings with quotes for syntactic safety
 *
 * Requires:
 * - `Char` enum to standardize symbols (quotes, brackets, etc.)
 *
 * @example
 * ```php
 * $this->betweenParentheses(['a', 'b']); // '(a b)'
 * $this->betweenBraces("id: 1");         // '{ id: 1 }'
 * $this->betweenBrackets([1, 2, 3]);     // '[1 2 3]'
 * $this->betweenQuotes("hello");         / "'hello'"
 * $this->betweenDoubleQuotes("world");   / '"world"'
 * $this->betweenChars("x", "<", ">");    / '<x>'
 * ```
 *
 * @package oihana\traits\strings
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait BetweenTrait
{
    use ExpressionTrait ;

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
}