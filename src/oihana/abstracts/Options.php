<?php

namespace oihana\abstracts;

use InvalidArgumentException;
use ReflectionException;

use oihana\enums\Char;
use oihana\interfaces\Optionable;
use oihana\reflections\traits\ReflectionTrait;

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
     * The default prefix of the options (by default "--").
     * @var string
     */
    public static string $prefix = Char::DOUBLE_HYPHEN ;

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
     * Returns the full options expression with the specific constant definitions.
     * @param string $clazz
     * @return string
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function getOptions( string $clazz ):string
    {
        if ( !is_a( $clazz, Optionable::class , true ) )
        {
            throw new InvalidArgumentException( sprintf
            (
                __METHOD__ . " failed, the passed-in class %s must implement the Optionable interface." ,
                $clazz
            )) ;
        }

        $expression = [] ;
        $properties = $this->getPublicProperties( static::class ) ;
        foreach( $properties as $property )
        {
            $name  = $property->getName() ;
            $value = $this->{ $name } ?? null ;
            if( isset( $value ) )
            {
                $option = $clazz::getOption( $name , static::$prefix ) ;
                if( isset( $option ) )
                {
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
        }

        return implode( Char::SPACE , $expression ) ;
    }

    /**
     * Returns the string expression of the object.
     * @return string
     * @throws ReflectionException
     */
    public function __toString() : string
    {
        return $this->getOptions( static::class ) ;
    }
}