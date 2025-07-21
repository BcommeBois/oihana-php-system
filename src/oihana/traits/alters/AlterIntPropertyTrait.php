<?php

namespace oihana\traits\alters;

trait AlterIntPropertyTrait
{
    /**
     * Cast a value to integer.
     * If the value is an array, all elements in the array are casted.
     * ```
     * Property::PRICE => [ Alter::INT ] ,
     * ```
     * @param mixed $value
     * @param bool $modified
     * @return array|float
     */
    public function alterIntProperty( mixed $value , bool &$modified = false ): array|float
    {
        if( is_float( $value ) )
        {
            return $value ;
        }
        $modified = true ;
        if( is_array( $value ) )
        {
            return array_map( 'intval' , $value ) ;
        }
        return intval( $value ) ;
    }
}