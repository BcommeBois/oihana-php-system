<?php

namespace oihana\controllers\traits ;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\controllers\enums\ControllerParam;

/**
 * Provides helper methods to manage multilingual (i18n) content in controllers.
 *
 * This trait allows a controller to:
 *   - Initialize and store a list of valid languages (`$languages`) from an array or a PSR-11 container.
 *   - Filter client-provided arrays of translations to keep only supported languages.
 *   - Retrieve the appropriate translation for a given language, with fallback to the default language.
 *
 * Example usage:
 * ```php
 * // Initialize with an array of supported languages
 * $this->initializeLanguages(['languages' => ['fr', 'en']]);
 *
 * // Filter a multilingual array to keep only supported languages
 * $input =
 * [
 *    'fr' => 'Bonjour <span style="color:red">monde</span>',
 *    'en' => 'Hello <span style="color:red">world</span>',
 *    'de' => 'Hallo Welt'
 * ];
 *
 * $filtered = $this->filterLanguages($input);
 * // $filtered =
 * // [
 * //    'fr' => 'Bonjour <span style="color:red">monde</span>',
 * //    'en' => 'Hello <span style="color:red">world</span>'
 * // ]
 *
 * // Filter and remove inline styles for HTML output
 * $filteredHtml = $this->filterLanguages($input, true);
 * // $filteredHtml = [
 * //     'fr' => 'Bonjour <span>monde</span>',
 * //     'en' => 'Hello <span>world</span>'
 * // ]
 *
 * // Retrieve translation for a given language
 * $textEn = $this->translate($filtered, 'en'); // 'Hello <span>world</span>'
 *
 * // Retrieve translation with fallback to default language if requested language is missing
 * $textDe = $this->translate($filtered, 'de'); // 'Bonjour <span>monde</span>' (fallback to 'fr')
 * ```
 *
 * @property array<string> $languages List of supported language codes.
 */
trait LanguagesTrait
{
    /**
     * The enumeration of all valid languages used by the controller.
     * @var array<string>
     */
    public array $languages = [] ;

    /**
     * Filter an array of translations according to the available languages.
     *
     * This helper transforms an input array from the client to prepare a multilingual (i18n) property.
     * Example input: `[ 'fr' => 'bonjour', 'en' => 'hello' ]`
     *
     * @param array<string, mixed>|null $field The input array of translations.
     * @param bool $html If true, strip inline styles from HTML content.
     *
     * @return array<string, mixed>|null Filtered translations matching available languages, or null if input is empty.
     */
    public function filterLanguages( ?array $field , bool $html = false ) :?array
    {
        if( is_array( $field ) && !empty( $field ) )
        {
            $items = [] ;
            if( count( $this->languages ) > 0 )
            {
                foreach( $this->languages as $lang )
                {
                    if( isset( $field[ $lang ] ) )
                    {
                        $items[ $lang ] = $html // if html remove all styles
                                        ? preg_replace('/(<[^>]+) style=".*?"/i', '$1', $field[$lang] )
                                        : $field[ $lang ] ;
                    }
                }
            }
            return $items ;
        }
        return null ;
    }

    /**
     * Initialize the internal `$languages` property from an array or a PSR-11 container.
     *
     * @param array $init Optional initialization array, expected key: ControllerParam::LANGUAGES
     * @param ContainerInterface|null $container Optional PSR-11 container for fallback configuration
     *
     * @return static
     *
     * @throws ContainerExceptionInterface If the container fails to retrieve the languages
     * @throws NotFoundExceptionInterface If the requested languages key is not found in the container
     */
    public function initializeLanguages( array $init = [] , ?ContainerInterface $container = null ) :static
    {
        $languages = $init[ ControllerParam::LANGUAGES ] ?? null ;

        if( $languages == null && $container instanceof ContainerInterface && $container->has( ControllerParam::LANGUAGES ) )
        {
            $languages = $container->get( ControllerParam::LANGUAGES ) ;
        }

        $this->languages = is_array( $languages ) ? $languages : [] ;

        return $this ;
    }


    /**
     * Retrieve the translation for a specific language, or fallback to the default language.
     *
     * @param array<string, mixed> $texts Array of translations keyed by language codes.
     * @param string|null $lang The desired language code. If null, returns the full array.
     * @return mixed The translated text for the requested language, fallback, or full array if no match.
     */
    public function translate( array $texts , ?string $lang = null ) :mixed
    {
        if( $lang === null )
        {
            return $texts ;
        }
        else
        {
            if( array_key_exists( $lang , $texts ) )
            {
                return $texts[ $lang ] ;
            }
            else if( array_key_exists( $this->languages[0] , $texts ) ) // TODO verify
            {
                return $texts[ $this->languages[0] ] ;
            }
        }

        return $texts ;
    }
}