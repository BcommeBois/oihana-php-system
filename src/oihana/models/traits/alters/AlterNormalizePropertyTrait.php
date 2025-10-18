<?php

namespace oihana\models\traits\alters;

use oihana\core\arrays\CleanFlag ;

use function oihana\core\normalize ;

trait AlterNormalizePropertyTrait
{
    /**
     * Normalize a document property using configurable flags.
     *
     * The normalization can be customized via the `$definition` array:
     * - If empty or no flags provided, uses CleanFlag::DEFAULT | CleanFlag::RETURN_NULL
     * - If a flags value is provided at index 0, uses that instead
     *
     * @param mixed $value The value to normalize
     * @param array $definition Optional flags array: [CleanFlag value, ...other params]
     * @param bool $modified Reference flag indicating if the value was modified
     *
     * @return mixed The normalized value, or null if cleaned away
     *
     * @example
     * ```php
     * // Use default flags
     * $this->alterNormalizeProperty( $value );
     * // Uses: CleanFlag::DEFAULT | CleanFlag::RETURN_NULL
     *
     * // Use custom flags
     * $this->alterNormalizeProperty($value, [ CleanFlag::NULLS | CleanFlag::EMPTY ] );
     *
     * // Only remove nulls
     * $this->alterNormalizeProperty($value, [CleanFlag::NULLS]);
     * ```
     */
    public function alterNormalizeProperty
    (
        mixed $value ,
        array $definition = [] ,
        bool  &$modified  = false
    )
    : mixed
    {
        $flags = $definition[0] ?? ( CleanFlag::DEFAULT | CleanFlag::RETURN_NULL ) ;

        $newValue = normalize( $value , $flags ) ;

        if ( $newValue !== $value )
        {
            $modified = true;
        }

        return $newValue ;
    }
}