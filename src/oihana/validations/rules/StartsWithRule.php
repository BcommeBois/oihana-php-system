<?php

namespace oihana\validations\rules ;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

/**
 * Rule: Ensures that a given value starts with a specified prefix.
 *
 * This rule validates that the input value begins with a given string prefix.
 * It supports flexible comparison: the rule passes if
 *
 * - the value exactly matches the prefix,
 * - the prefix is empty,
 * - or the value starts with the prefix.
 *
 * Example:
 * ```php
 * $rule = new StartsWithRule( 'abc'] );
 * $rule->check('abcdef'); // true
 * $rule->check('xyz');    // false
 * ```
 *
 * @package oihana\api\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class StartsWithRule extends Rule
{
    /**
     * Creates a new StartsWithRule instance.
     * @param ?string $prefix The prefix expression to evaluate.
     */
    public function __construct( ?string $prefix = null )
    {
        $this->prefix( $prefix ) ;
    }

    /**
     * The rule name.
     */
    public const string NAME = 'startsWith' ;

    /**
     * The "prefix" parameter key.
     */
    public const string PREFIX = 'prefix' ;

    /**
     * @var array|string[] The list of required parameters.
     */
    protected array $fillableParams = [ self::PREFIX ] ;

    /**
     * The error message used when validation fails.
     * @var string
     */
    protected string $message = "The :attribute don't start with :prefix";

    /**
     * Checks if the provided value meets the required conditions based on the prefix parameter.
     *
     * @param mixed $value The value to be checked.
     * @return bool Returns true if the value equals the prefix, the prefix is empty,
     *              or the value starts with the prefix; otherwise, false.
     *
     * @throws ParameterException If the required parameter is missing.
     */
    public function check( mixed $value ) :bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams ) ;
        $prefix = $this->parameter(self::PREFIX );
        return $value == $prefix || empty($prefix) || str_starts_with( $value , $prefix ) ;
    }

    /**
     * Sets the prefix expression to evaluate.
     *
     * @param string|null $value The prefix string to use for validation.
     *
     * @return static Returns the current instance for method chaining.
     */
    public function prefix( ?string $value ) :Rule
    {
        $this->params[ self::PREFIX ] = $value  ;
        return $this;
    }
}