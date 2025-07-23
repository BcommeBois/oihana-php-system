<?php

namespace oihana\abstracts;

use oihana\reflections\traits\ConstantsTrait;

use function oihana\core\strings\hyphenate;

/**
 * Base class for options definitions.
 */
abstract class Option
{
    use ConstantsTrait ;

    /**
     * Returns the command line option expression from a specific option.
     * @param string $option
     * @return string
     */
    public static function getCommandOption( string $option ):string
    {
        return hyphenate( $option ) ;
    }
}