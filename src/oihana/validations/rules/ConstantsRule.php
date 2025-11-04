<?php

namespace oihana\validations\rules ;

use InvalidArgumentException;
use oihana\reflect\traits\ConstantsTrait;
use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

use function oihana\reflect\helpers\useConstantsTrait;

/**
 * Rule: Validates that a given value is part of the constants defined
 * in a class using {@see ConstantsTrait}.
 *
 * **Usage**
 *
 * This generic rule ensures that the provided value matches one of the
 * allowed constants from any class that uses the {@see ConstantsTrait}.
 *
 * **Examples**
 *
 * ```php
 * use oihana\validations\rules\ConstantsRule;
 * use Somnambulist\Components\Validation\Validator;
 * use xyz\oihana\schema\constants\JWTAlgorithm;
 *
 * // Validate against all constants in a class
 * $rule = new ConstantsRule(JWTAlgorithm::class);
 *
 * $validator = new Validator
 * (
 *     ['alg' => 'HS256'],
 *     ['alg' => [$rule]]
 * );
 *
 * $validator->passes(); // true
 *
 * // Validate against a subset of constants
 * $rule = new ConstantsRule(JWTAlgorithm::class, ['HS256', 'RS256']);
 *
 * $validator = new Validator(
 *     ['alg' => 'RS512'],
 *     ['alg' => [$rule]]
 * );
 *
 * $validator->fails(); // true
 * ```
 *
 * **Custom Error Messages**
 *
 * You can customize the error message:
 *
 * ```php
 * $rule = new ConstantsRule(Status::class);
 * $rule->message(':attribute must be a valid status.');
 * ```
 *
 * @see ConstantsTrait The trait that provides constant enumeration capabilities.
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz
 * @since   1.0.0
 */
class ConstantsRule extends Rule
{
    /**
     * Creates a new ConstantsRule instance.
     *
     * @param class-string<ConstantsTrait> $className The fully qualified class name that uses ConstantsTrait.
     * @param array|null                   $cases     Optional list of allowed constant values.
     *                                                Defaults to all values from the class's enums() method.
     *
     * @throws InvalidArgumentException If the class doesn't use ConstantsTrait.
     *
     * @example
     * ```php
     * // All constants
     * $rule = new ConstantsRule(JWTAlgorithm::class);
     *
     * // Subset of constants
     * $rule = new ConstantsRule(JWTAlgorithm::class, ['HS256', 'RS256']);
     * ```
     */
    public function __construct( string $className, ?array $cases = null )
    {
        $this->className( $className ) ;
        $this->cases( empty($cases) ? $className::enums() : $cases ) ;
    }

    /**
     * The parameter key for the constants class name.
     */
    public const string CLASS_NAME = 'className' ;

    /**
     * The parameter key used to store the valid constants list.
     */
    public const string CASES = 'cases' ;

    /**
     * The list of valid constant values used by this rule.
     * @var array
     */
    protected array $cases = [] ;

    /**
     * The parameters that must be present for the rule to function.
     * @var string[]
     */
    protected array $fillableParams = [ self::CLASS_NAME , self::CASES ] ;

    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute is not a valid value.";

    /**
     * Sets or overrides the list of valid constant values.
     *
     * If `$cases` is `null` or empty, it defaults to all enums from the class.
     *
     * @param array|null $cases The constant values to allow in this rule.
     *
     * @return static
     *
     * @example
     * ```php
     * $rule = (new ConstantsRule(JWTAlgorithm::class))->cases(['HS256', 'HS512']);
     * ```
     */
    public function cases( ?array $cases = null ): static
    {
        $className = $this->parameter(self::CLASS_NAME) ;

        if ( empty( $cases ) && !empty( $className ) )
        {
            $cases = $className::enums() ;
        }

        $this->params[ self::CASES ] = $cases ?? [] ;
        return $this ;
    }

    /**
     * Sets the class name that provides the constants.
     *
     * @param class-string<ConstantsTrait> $className The fully qualified class name.
     *
     * @return static
     *
     * @example
     * ```php
     * $rule = (new ConstantsRule(Status::class))->className(Priority::class);
     * ```
     */
    public function className( string $className ): static
    {
        if ( !useConstantsTrait( $className ) )
        {
            throw new InvalidArgumentException( sprintf
            (
                'Class "%s" must use ConstantsTrait to work with ConstantsRule.' ,
                $className
            )) ;
        }
        $this->params[ self::CLASS_NAME ] = $className ;
        return $this ;
    }

    /**
     * Checks if the given value is one of the allowed constant values.
     *
     * @param mixed $value The value to validate.
     *
     * @return bool True if the value is a valid algorithm, false otherwise.
     *
     * @throws ParameterException If required parameters have not been initialized.
     *
     * @example
     * ```php
     * $rule = new ConstantsRule( JWTAlgorithm::class );
     * $rule->check('RS512'); // true
     * $rule->check('MD5');   // false
     * ```
     */
    public function check( mixed $value ): bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams ) ;
        return in_array( $value , $this->parameter( self::CASES ) , true ) ;
    }
}