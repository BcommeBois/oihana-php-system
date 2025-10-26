<?php

namespace oihana\validations\rules ;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

/**
 * Ensures that a given value is instance of the specified class name.
 *
 * Example:
 * ```php
 * $rule = new InstanceOfRule(DateTime::class);
 * $rule->check(new DateTime()) ; // true
 * $rule->check(new stdClass()) ; // false
 * $rule-
 *
 * @package oihana\api\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class InstanceOfRule extends Rule
{
    /**
     * Creates a new InstanceOfRule instance.
     * @param ?string $className The class name expression to evaluate.
     */
    public function __construct( ?string $className = null )
    {
        $this->className( $className ) ;
    }

    /**
     * The "className" parameter key.
     */
    public const string CLASS_NAME = 'className' ;

    /**
     * The rule name.
     */
    public const string NAME = 'instanceOf' ;

    /**
     * @var array|string[] The list of required parameters.
     */
    protected array $fillableParams = [ self::CLASS_NAME ] ;

    /**
     * The error message used when validation fails.
     * @var string
     */
    protected string $message = ":attribute must be an instanceof :className";

    /**
     * Checks if the provided value is an instance of the configured class name.
     *
     * @param mixed $value The value to check.
     *
     * @return bool Returns true if the value is an instance of the specified class; otherwise, false.
     *
     * @throws ParameterException If the required parameter `className` is missing.
     */
    public function check( mixed $value ) :bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams ) ;
        $className = $this->parameter(self::CLASS_NAME );
        return !empty($className) && class_exists( $className ) && $value instanceof $className ;
    }

    /**
     * Sets the class name to check instances against.
     *
     * @param string|null $value The fully qualified class name.
     *
     * @return static Returns the current instance for fluent chaining.
     */
    public function className( ?string $value ) :static
    {
        $this->params[ self::CLASS_NAME ] = $value  ;
        return $this;
    }
}