<?php

namespace oihana\validations\rules ;

use oihana\enums\PostalCodePattern;

use org\iso\ISO3166_1;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;

use function oihana\core\arrays\toArray;

/**
 * Validates a postal code for a given country.
 *
 * @see PostalCode
 *
 * @example
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 * use xyz\enums\PostalCode ;
 * use xyz\validators\rules\PostalCode ;
 *
 * $validator = new Factory();
 *
 * $validator->addRule('postalCode', new PostalCode() );
 * // or
 * $validator->addRule('postalCode', new PostalCode( PostalCode::IT ) );
 * ```
 *
 * Basic Usage
 * ```
 *  $validation = $validator->validate( $_POST,
 *  [
 *     'postalCode' => 'required|postalCode'
 *  ]);
 * ```
 *
 * Enforce the default postal code pattern with a default ISO 3166-1 country code parameter : CH, DE, ES, FR, GB, IT, US
 * ...
 * $validation = $validator->validate( $_POST,
 * [
 *    'postalCode' => 'required|postalCode:IT'
 * ]);
 * ```
 */
class PostalCodeRule extends Rule
{
    /**
     * Creates a new PostalCodeRule instance.
     *
     * @param array|string|null $country An ISO 3166-1 alpha-2 country code or a list of valid country codes.
     * @param string            $default The default ISO 3166-1 alpha-2 country code (By default ISO3166_1::FR)
     */
    public function __construct
    (
        array|string|null $country = null ,
        string            $default = ISO3166_1::FR ,
    )
    {
        $this->default = PostalCodePattern::includes( $default ) ? $default : ISO3166_1::FR;
        $this->country( $country ) ;
    }

    /**
     * The country parameter.
     */
    public const string COUNTRY = 'country' ;

    /**
     * The default ISO 3166-1 alpha-2 country code.
     * @var string
     */
    protected string $default = ISO3166_1::FR ;

    /**
     * The internal list of fillable parameters.
     * @var array
     */
    protected array $fillableParams = [ self::COUNTRY ];

    /**
     * The internal message pattern.
     * @var string
     */
    protected string $message = ":attribute is not a valid postal code.";

    /**
     * The country code of the postalCode regex pattern.
     *
     * @param array|string|null $value The country code of the postal code.
     * @return static
     *
     * @example
     * ```php
     * $lang = 'it' ;
     * $validator = new Validator() ;
     * $validator->setValidator( 'postalCode' , new PostalCodeRule() ) ;
     * $validation = $validator->validate( [ 'postalCode' => 45 ] , [ 'postalCode' => 'required|postalCode:it' ] ) ;
     * ```
     */
    public function country( array|string|null $value ) :static
    {
        $this->params[ self::COUNTRY ] = array_map('strtoupper', toArray( $value ?? $this->default ) ) ;
        return $this;
    }

    /**
     * Checks whether the given value satisfies the condition.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value satisfies the condition.
     *
     * @throws ParameterException
     */
    public function check( mixed $value ): bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams ) ;

        if ( !is_string( $value ) )
        {
            return false ;
        }

        $countries = $this->parameter(self::COUNTRY , $this->default );

        foreach ( $countries as $country )
        {
            $pattern = PostalCodePattern::getPattern( $country ) ;
            if ( $pattern !== null && PostalCodePattern::isValid( $value , $country ) )
            {
                return true ;
            }
        }

        return false ;
    }
}