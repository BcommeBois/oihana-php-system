<?php

namespace oihana\abstracts;

use inu\wp\commands\wp\enums\core\CheckUpdateOption;
use InvalidArgumentException;
use ReflectionException;

use oihana\enums\Char;
use oihana\reflections\traits\ReflectionTrait;
use ReflectionProperty;

/**
 * Base class for options definitions.
 */
abstract class Options
{
    /**
     * Initializes options from an array or object.
     * @param array|object|null $init
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
     * Creates a new instance of the called class with optional options.
     *
     * @param array|Options|null $options
     * @return Options
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
     * Returns the full command line options expression with the specific definition.
     * @param string $clazz The enumeration of options.
     * @param ?string $prefix The optional prefix to prepend before the option expression.
     * @return string
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getOptions( string $clazz , ?string $prefix = null ):string
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

        /**
         * @var ReflectionProperty $property
         */
        foreach( $properties as $property )
        {
            $name  = $property->getName() ;
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
                        $expression[] = $option . Char::SPACE . json_encode( $item , JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ;
                    }
                }
                elseif ( is_bool ( $value ) )
                {
                    $expression[] = $option ;
                }
                else
                {
                    $expression[] = $option . Char::SPACE . json_encode( $value , JSON_UNESCAPED_SLASHES ) ;
                }
            }
        }

        return implode( Char::SPACE , $expression ) ;
    }

    /**
     * Returns the string expression of the object.
     * @return string
     */
    public function __toString() : string
    {
        return Char::EMPTY ;
    }
}