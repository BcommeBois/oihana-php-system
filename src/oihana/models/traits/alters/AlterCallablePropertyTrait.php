<?php

namespace oihana\models\traits\alters;

trait AlterCallablePropertyTrait
{
    /**
     * Call a function to alter a property.
     * @param mixed $value
     * @param array $definition
     * @param bool $modified
     * @return mixed
     */
    public function alterCallableProperty( mixed $value , array $definition = [] , bool &$modified = false ): mixed
    {
        $function = array_shift( $definition ) ;
        if( is_callable( $function ) )
        {
            $value = $function( ...([ $value , ...$definition ]) ) ;
            $modified = true ;
        }
        return $value ;
    }
}