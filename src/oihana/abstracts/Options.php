<?php

namespace oihana\abstracts;

use InvalidArgumentException;
use JsonSerializable;
use oihana\interfaces\Cloneable;
use ReflectionException;

use oihana\enums\Char;
use oihana\reflections\traits\ReflectionTrait;
use function oihana\core\strings\formatFromDocument;

/**
 * Abstract base class for defining configurable options.
 *
 * Features:
 * - Automatic hydration from arrays or objects.
 * - Reflection-based listing of public properties.
 * - Template string formatting with placeholders.
 * - CLI-compatible string generation from object state.
 */
abstract class Options implements Cloneable, JsonSerializable
{
    /**
     * Initializes the object using an associative array or object.
 *
     * Only public properties declared on the class will be set.
     * Unknown or non-public properties are silently ignored.
     *
     * @param array|object|null $init Initial values to populate the instance.
     */
    public function __construct( array|object|null $init = null )
    {
        if( isset( $init ) )
        {
            foreach ( $init as $key => $value )
            {
                if( property_exists( $this , $key ) )
                {
                    $this->{ $key } = $value ;
                }
            }
        }
    }

    use ReflectionTrait ;

    /**
     * Creates a deep copy of the current instance.
     *
     * This performs a full deep copy by serializing and unserializing the object.
     * Useful when duplicating options to avoid shared references.
     *
     * @return static A new cloned instance.
     */
    public function clone(): static
    {
        return unserialize( serialize( $this ) );
    }

    /**
     * Instantiates the class from an array or another Options instance.
     *
     * - If $options is an array, it is passed to the constructor.
     * - If $options is already an Options instance, it is returned as-is.
     * - If null, a new empty instance is returned.
     *
     * @param array|Options|null $options  The initial values or existing options instance.
     * @return static                      A new or reused instance of the called class.
     */
    public static function create( array|Options|null $options = null ) :Options
    {
        if( is_array( $options ) )
        {
            return new static( $options ) ;
        }
        return $options instanceof Options ? $options : new static() ;
    }

    /**
     * Formats a template string by replacing placeholders like `{{property}}` with
     * the corresponding public property values of the current object.
     *
     * Supports custom placeholder delimiters. If a referenced property is not defined
     * or is null, it is replaced with an empty string.
     *
     * @param string|null $template The template string. Placeholders must match the format `{{property}}` or a custom format.
     * @param string       $prefix   The prefix that begins a placeholder (default: `{{`).
     * @param string       $suffix   The suffix that ends a placeholder (default: `}}`).
     *
     * @return string|null The formatted string, or `null` if the template is invalid.
     *
     * @example
     * ```php
     * $opts = new ServerOptions();
     * $opts->domain    = 'example.com';
     * $opts->subdomain = 'www';
     *
     * echo $opts->format('https://{{subdomain}}.{{domain}}');
     * // → https://www.example.com
     *
     * echo $opts->format('Hello %%domain%%!', '%%', '%%');
     * // → Hello example.com!
     *
     * echo $opts->format('Missing: {{nonexistent}}');
     * // → Missing:
     * ```
     */
    public function format( ?string $template = null , string $prefix = '{{' , string $suffix = '}}' ): ?string
    {
        if ( !is_string( $template ) || $template === Char::EMPTY )
        {
            return null;
        }

        $escapedPrefix = preg_quote( $prefix , '/' ) ;
        $escapedSuffix = preg_quote( $suffix , '/' ) ;
        $pattern       = '/' . $escapedPrefix . '(\w+)' . $escapedSuffix . '/' ;

        preg_match_all( $pattern , $template , $matches ) ;
        $properties = $matches[1] ?? [];

        $placeholders = [] ;
        $replacements = [] ;

        foreach ( $properties as $prop )
        {
            if( property_exists( $this , $prop ) )
            {
                $placeholders[] = $prefix . $prop . $suffix;
                $value = $this->{ $prop } ?? Char::EMPTY ;
                if( is_array( $value ) )
                {
                    $value = json_encode( $value , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;
                }
                $replacements[] = $value ;
            }
        }

        return str_replace( $placeholders , $replacements , $template ) ;
    }

    /**
     * Recursively formats all string values in an array using the current object properties.
     *
     * @param array  &$data  The input array (modified by reference).
     * @param string $prefix Placeholder prefix.
     * @param string $suffix Placeholder suffix.
     *
     * @return array The formatted array.
     */
    public function formatArray( array &$data , string $prefix = '{{' , string $suffix = '}}' ): array
    {
        foreach ( $data as $key => $value )
        {
            if ( is_array( $value ) )
            {
                $data[ $key ] = $this->formatArray( $value , $prefix , $suffix ); ;
            }
            elseif ( is_string( $value ) )
            {
                $data[ $key ] = $this->format( $value , $prefix , $suffix );
            }
        }
        return $data ;
    }

    /**
     * Formats all public string properties using the object’s own values.
     *
     * @param string $prefix Placeholder prefix (default `{{`).
     * @param string $suffix Placeholder suffix (default `}}`).
     *
     * @return void
     *
     * @throws ReflectionException
     *
     * @example
     * ```php
     * $opts->url = 'https://{{host}}';
     * $opts->host = 'example.com';
     * $opts->formatProperties();
     * echo $opts->url; // → https://example.com
     * ```
     */
    public function formatProperties( string $prefix = '{{', string $suffix = '}}' ): void
    {
        foreach ( $this->getPublicProperties( static::class ) as $property )
        {
            $name  = $property->getName() ;
            $value = $this->{ $name } ;

            if ( is_string( $value ) && str_contains( $value, $prefix ) && str_contains( $value, $suffix ) )
            {
                $this->{ $name } = $this->format( $value , $prefix , $suffix ) ;
            }
        }
    }

    /**
     * Formats all public string properties using external data instead of internal values.
     *
     * @param array|object $document Associative array or an object of placeholder values.
     * @param string       $prefix   Placeholder prefix (default `{{`).
     * @param string       $suffix   Placeholder suffix (default `}}`).
     *
     * @return void
     *
     * @throws ReflectionException
     * @example
     * ```php
     * $opts->url = 'https://{{host}}/{{path}}';
     * $opts->formatFromDocument(['host' => 'example.com', 'path' => 'docs']);
     * echo $opts->url; // → https://example.com/docs
     * ```
     */
    public function formatFromDocument( array|object $document , string $prefix = '{{' , string $suffix = '}}' ): void
    {
        foreach ( $this->getPublicProperties( static::class ) as $property )
        {
            $name     = $property->getName() ;
            $template = $this->{ $name } ?? null ;

            if ( is_string( $template ) && str_contains( $template , $prefix ) && str_contains( $template , $suffix ) )
            {
                $this->{ $name } = formatFromDocument( $template , $document , $prefix , $suffix );
            }
        }
    }

    /**
     * Builds a command-line string of options based on the current object state.
     *
     * Only public properties with a non-null value will be considered,
     * unless explicitly excluded via the `$excludes` parameter.
     * The name of each property must match an option defined in the `$clazz` enumeration.
     *
     * @param string|null $clazz     Fully qualified class name extending the Option enum.
     * @param string|null $prefix    Optional prefix to prepend before each option (e.g. "--").
     * @param array|null  $excludes  List of property names to exclude from the output.
     * @param string      $separator The separator between the option's name and value (Default " ").
     *
     * @return string                The formatted command-line options string or an empty string if the clazz parameter is null.
     *
     * @throws InvalidArgumentException If $clazz is not a subclass of Option.
     * @throws ReflectionException      If property reflection fails.
     */
    public function getOptions( ?string $clazz = null , ?string $prefix = null , ?array $excludes = null , string $separator = Char::SPACE ):string
    {
        if( !isset( $clazz ) )
        {
            return Char::EMPTY ;
        }

        if ( !is_a( $clazz, Option::class , true ) )
        {
            throw new InvalidArgumentException( sprintf
            (
                __METHOD__ . " failed, the passed-in class %s must inherit the Option class." ,
                $clazz
            )) ;
        }

        $expression = [] ;

        $properties = $this->getPublicProperties( static::class ) ;

        foreach( $properties as $property )
        {
            $name  = $property->getName() ;

            if ( is_array( $excludes ) && in_array( $name , $excludes , true ) )
            {
                continue ;
            }

            $value = $this->{ $name } ?? null ;
            if( isset( $value ) )
            {
                $option = $clazz::getCommandOption( $name ) ;
                if( isset( $prefix ) )
                {
                    $option = $prefix . $option ;
                }

                if( is_array( $value ) && count( $value ) > 0 )
                {
                    foreach ( $value as $item )
                    {
                        $expression[] = $option . $separator . json_encode( $item , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;
                    }
                }
                elseif ( $value === true )
                {
                    $expression[] = $option ;
                }
                else
                {
                    $expression[] = $option . $separator . json_encode( $value , JSON_UNESCAPED_SLASHES ) ;
                }
            }
        }

        return implode( Char::SPACE , $expression ) ;
    }

    /**
     * Returns data to be serialized by json_encode().
     *
     * This implementation converts the cleaned public properties of the object
     * to an object representation, ensuring that json_encode always returns
     * a JSON object (e.g. `{}` instead of `[]` when empty).
     *
     * @return object An object representing the public non-empty properties.
     *
     * @throws ReflectionException
     * @example
     * @example
     * ```php
     * $options = new ServerOptions
     * ([
     *     'host' => 'localhost',
     *     'port' => 8080,
     *     'debug' => null,
     * ]);
     *
     * echo json_encode($options);
     * // → {"host":"localhost","port":8080}
     *
     * $options = new ServerOptions([ 'host' => '' , 'debug' => null ]);
     *
     * echo json_encode($options); // → {}
     */
    public function jsonSerialize(): object
    {
        return (object) $this->toArray(true) ;
    }

    /**
     * Converts the current object to an associative array.
     *
     * Only public properties defined on the class are included.
     * Useful for serialization, debugging, or exporting the object state.
     *
     * @param bool $clear If true, removes entries with null values. Default: false.
     *
     * @return array<string, mixed> The associative array representation of the object.
     *
     * @throws ReflectionException
     * @example
     * ```php
     * $options = new ServerOptions([
     *     'host' => 'localhost',
     *     'port' => 8080,
     *     'debug' => null,
     *     'empty' => '',
     *     'values' => [],
     * ]);
     *
     * print_r($options->toArray());
     * // → [ 'host' => 'localhost', 'port' => 8080, 'debug' => null , 'empty' => '' , 'values' => [] ]
     *
     * print_r($options->toArray(true));
     * // → [ 'host' => 'localhost', 'port' => 8080 ]
     * ```
     */
    public function toArray( bool $clear = false ): array
    {
        $data = [];
        foreach ( $this->getPublicProperties(static::class ) as $property )
        {
            $name  = $property->getName() ;
            $value = $this->{ $name } ?? null ;

            if( ( is_string( $value ) && trim($value) === Char::EMPTY ) || $value === [] )
            {
                $value = null ;
            }

            if ( !$clear || $value !== null )
            {
                $data[ $name ] = $value ;
            }
        }
        return $data ;
    }

    /**
     * Returns a string representation of the object.
     *
     * Override this method in child classes to provide a meaningful string output.
     *
     * @return string  Default implementation returns an empty string.
     */
    public function __toString() : string
    {
        return Char::EMPTY ;
    }
}