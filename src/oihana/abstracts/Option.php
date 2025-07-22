<?php

namespace oihana\abstracts;

use oihana\enums\Char;
use oihana\interfaces\Optionable;
use oihana\reflections\traits\ConstantsTrait;
use RuntimeException;

/**
 * Base class for options definitions.
 */
abstract class Option implements Optionable
{
    use ConstantsTrait ;

    protected static ?string $COMMANDS = null ;
    private   static ?array  $OPTIONS = null ;

    /**
     * Returns the option value with the specific option property name.
     * @param string $name The name of the option constant.
     * @param string $prefix The optional prefix to append before the option name.
     * @return string|null
     */
    public static function getOption( string $name , string $prefix = Char::EMPTY ): ?string
    {
        if( static::$OPTIONS === null )
        {
            if ( !isset( static::$COMMANDS ) )
            {
                throw new RuntimeException("A class must be defined in static::\$COMMANDS" );
            }

            if ( !class_exists( static::$COMMANDS ) )
            {
                throw new RuntimeException("The class defined in static::\$COMMANDS does not exist: " . static::$COMMANDS);
            }

            if ( !method_exists( static::$COMMANDS , 'getAll' ) )
            {
                throw new RuntimeException("The class " . static::$COMMANDS . " must have a static getAll() method.");
            }

            static::$OPTIONS = static::$COMMANDS::getAll() ;
        }

        $option = static::$OPTIONS[ static::getConstant( $name ) ] ?? null;

        if( is_string( $option ) )
        {
            $option = $prefix . $option ;
        }

        return $option ;
    }
}