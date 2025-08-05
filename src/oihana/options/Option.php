<?php

namespace oihana\options;

use oihana\reflections\traits\ConstantsTrait;

use function oihana\core\strings\hyphenate;

/**
 * Abstract base class for mapping property names to command-line option names.
 *
 * Typically used with {@see Options::getOptions()} to convert property keys
 * into hyphenated command-line flags (e.g. "dryRun" → "dry-run").
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