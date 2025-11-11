<?php

namespace oihana\validations\rules ;

use Somnambulist\Components\Validation\Exceptions\ParameterException;
use Somnambulist\Components\Validation\Rule;
use function oihana\core\arrays\toArray;

/**
 * Validates a multilingual (i18n) field ensuring:
 *   - Only allowed language codes are present.
 *   - Values are strings or null.
 *
 * This rule can validate arrays or objects containing translations. It is useful
 * for payloads like:
 * ```php
 * $payload =
 * [
 *     'description' =>
 *     [
 *         'fr' => 'Bonjour',
 *         'en' => 'Hello',
 *         'de' => null
 *     ]
 * ];
 * ```
 *
 * You can restrict which languages are valid by passing an array or a single
 * language code to the constructor:
 * ```php
 * $rule = new I18nRule(['fr', 'en']);
 * ```
 *
 * ### Usage Examples
 * ```php
 * use Somnambulist\Components\Validation\Factory;
 * use oihana\validations\rules\I18nRule;
 *
 * $validator = new Factory();
 * $validator->addRule('i18n', new I18nRule(['fr','en']));
 *
 * $payload = ['description' => ['fr'=>'Bonjour','en'=>'Hello']];
 * $validation = $validator->validate($payload, ['description' => 'required|i18n']);
 *
 * $validation->passes(); // true
 *
 * $payload = ['description' => ['fr'=>'Bonjour','de'=>'Hallo']];
 * $validation = $validator->validate($payload, ['description' => 'required|i18n']);
 *
 * $validation->passes(); // false, 'de' not allowed
 * ```
 *
 * @package oihana\validations\rules
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
class I18nRule extends Rule
{
    /**
     * Creates a new LanguagesRule instance.
     *
     * @param array|string|null $languages Array or list of allowed language codes.
     */
    public function __construct( array|string|null $languages = null )
    {
        $this->languages( $languages ) ;
    }

    /**
     * The 'languages' parameter key.
     */
    public const string LANGUAGES = 'languages';

    /**
     * The list of required parameters.
     * @var array|string[]
     */
    protected array $fillableParams = [ self::LANGUAGES ] ;

    /**
     * The error message used when validation fails.
     * @var string
     */
    protected string $message = ':attribute contains invalid translations or unsupported languages.';

    /**
     * Sets allowed languages.
     *
     * @param array|string|null $languages
     * @return static
     */
    public function languages( array|string|null $languages ): static
    {
        $this->params[ self::LANGUAGES ] = array_map('strtolower' , toArray($languages ?? [] ) ) ;
        return $this ;
    }

    /**
     * Validates that each field in the payload contains only allowed languages
     * and that values are string or null.
     *
     * @param mixed $value array/object of translations
     * @return bool
     * @throws ParameterException
     */
    public function check( mixed $value ): bool
    {
        $this->assertHasRequiredParameters( $this->fillableParams ) ;

        if ( !is_array( $value ) && !is_object( $value ) )
        {
            return false;
        }

        $payload = is_object( $value ) ? (array) $value : $value ;

        if ( empty( $payload ) )
        {
            return true ;
        }

        $allowedLanguages = $this->parameter(self::LANGUAGES , [] ) ;

        foreach ( $payload as $lang => $text )
        {
            if ( !in_array( $lang , $allowedLanguages , true ) )
            {
                return false ; // The lang field is not allowed
            }

            if ( !is_string( $text ) && !is_null( $text ) )
            {
                return false ; // Only string or null allowed
            }
        }

        return true ;
    }
}