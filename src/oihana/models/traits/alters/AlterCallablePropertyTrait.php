<?php

namespace oihana\models\traits\alters;

use function oihana\core\callables\resolveCallable;

trait AlterCallablePropertyTrait
{
    /**
     * Call a function to alter a property.
     * @param mixed $value
     * @param array $definition
     * @param bool $modified
     * @return mixed
     */
    public function alterCallableProperty
    (
        mixed $value ,
        array $definition = [] ,
        bool &$modified   = false
    )
    : mixed
    {
        $callable = array_shift($definition);

        if ( is_string( $callable ) )
        {
            $callable = resolveCallable( $callable ) ;
        }

        if( $callable !== null && is_callable( $callable ) )
        {
            $value = $callable( $value , ...$definition );
            $modified = true;
        }

        return $value;

    }
}