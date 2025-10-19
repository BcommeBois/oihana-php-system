<?php

namespace oihana\validations\rules ;

use oihana\enums\Char;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

/**
 * Rule: Ensures that a given value matches a valid color expression (e.g. "#ff0000").
 *
 * This rule checks whether a value matches a configurable regular expression pattern
 * representing a color value. By default, it validates 6-digit hexadecimal color codes
 * starting with a '#' prefix.
 *
 * Example:
 * ```php
 * $rule = new ColorRule();
 * $rule->check('#ff00ff'); // true
 * $rule->check('ff00ff');  // false
 *
 * $custom = new ColorRule(['prefix' => '', 'pattern' => '/^%s[A-F0-9]{6}$/']);
 * $custom->check('FF00FF'); // true
 * ```
 *
 * @package oihana\api\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class ColorRule extends Rule
{
    /**
     * Creates a new ColorRule instance.
     *
     * @param array $init Optional initialization parameters (keys: 'pattern', 'prefix').
     */
    public function __construct( array $init = [] )
    {
        $prefix  = $init[ self::PREFIX  ] ?? Char::NUMBER ; // #
        $pattern = $init[ self::PATTERN ] ?? self::DEFAULT_PATTERN ; // RRGGBB
        $this->pattern( $pattern )->prefix( $prefix ) ;
    }

    /**
     * Default regex pattern format (supports prefix substitution via sprintf).
     * The "%s" placeholder will be replaced by the prefix value.
     */
    public const string DEFAULT_PATTERN = '/^%s[a-fA-F0-9]{6}$/';

    /**
     * The rule name.
     */
    public const string NAME = 'color';

    /**
     * The 'pattern' parameter key.
     */
    public const string PATTERN = 'pattern' ;

    /**
     * The 'prefix' parameter key.
     */
    public const string PREFIX = 'prefix' ;

    /**
     * @var array|string[]
     */
    protected array $fillableParams = [ self::PATTERN , self::PREFIX ];

    /**
     * The default error message used when validation fails.
     * @var string
     */
    protected string $message = ":attribute must be a valid color expression, ex: :prefixff0000";

    /**
     * Validates whether the given value matches the defined color pattern.
     *
     * @param mixed $value The value to validate.
     *
     * @return bool True if the value matches the pattern; false otherwise.
     *
     * @throws ParameterException If required parameters are missing.
     */
    public function check( mixed $value ): bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams );

        $prefix  = (string) $this->parameter( self::PREFIX  ) ;
        $pattern = (string) $this->parameter (self::PATTERN ) ;

        if ( !is_string($value) )
        {
            return false ;
        }

        // Inject prefix into pattern (pattern must contain %s)
        $regex = str_contains( $pattern , '%s' ) ? sprintf($pattern, preg_quote($prefix, '/') ) : $pattern ;

        return (bool) preg_match( $regex , $value ) ;
    }

    /**
     * Sets the regex pattern used to validate color expressions.
     *
     * The pattern should include a "%s" placeholder where the prefix will be injected.
     * Example: "/^%s[a-fA-F0-9]{6}$/i"
     *
     * @param string $value The regex pattern format.
     * @return static Returns the current instance for method chaining.
     */
    public function pattern( string $value = self::DEFAULT_PATTERN ) :ColorRule
    {
        $this->params[ self::PATTERN ] = $value ;
        return $this;
    }

    /**
     * Sets the prefix of the color expression (default '#').
     *
     * @param string $value The prefix character(s) for color expressions.
     * @return static Returns the current instance for method chaining.
     */
    public function prefix( string $value = Char::NUMBER ) :ColorRule
    {
        $this->params[ self::PREFIX ] = $value ;
        return $this;
    }
}