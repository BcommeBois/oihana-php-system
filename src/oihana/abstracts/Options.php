<?php

namespace oihana\abstracts;

use InvalidArgumentException;
use oihana\interfaces\Cloneable;
use ReflectionException;

use oihana\enums\Char;
use oihana\reflections\traits\ReflectionTrait;

/**
 * Abstract base class for defining configurable options.
 *
 * Provides automatic hydration from arrays or objects,
 * reflective property listing, and command-line formatting.
 */
abstract class Options implements Cloneable
{
    /**
     * Initializes options from an associative array or object.
     *
     * Properties in the input must match public properties defined on the class.
     * Unknown properties are silently ignored.
     *
     * @param array|object|null $init  Initial values to populate the instance.
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
     * This method clones the current object and its properties.
     * Useful when you want to duplicate options without affecting
     * the original reference.
     *
     * @return static A new instance.
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
     * Builds a command-line string of options based on the current object state.
     *
     * Only public properties with a non-null value will be considered,
     * unless explicitly excluded via the `$excludes` parameter.
     * The name of each property must match an option defined in the `$clazz` enumeration.
     *
     * @param string      $clazz     Fully qualified class name extending the Option enum.
     * @param string|null $prefix    Optional prefix to prepend before each option (e.g. "--").
     * @param array|null  $excludes  List of property names to exclude from the output.
     * @param string      $separator The separator between the option's name and value (Default " ").
     *
     * @return string                The formatted command-line options string.
     *
     * @throws InvalidArgumentException If $clazz is not a subclass of Option.
     * @throws ReflectionException      If property reflection fails.
     */
    public function getOptions( string $clazz , ?string $prefix = null , ?array $excludes = null , string $separator = Char::SPACE ):string
    {
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
                elseif ( is_bool ( $value ) )
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