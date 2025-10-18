<?php

namespace oihana\models\traits\alters;

trait AlterFloatPropertyTrait
{
    /**
     * Cast a value to float. If the value is an array, all elements in the array are casted.
     * ```
     * Property::PRICE => [ Alter::FLOAT ] ,
     * ```
     * @param mixed $value
     * @param bool $modified
     * @return array|float
     */
    public function alterFloatProperty( mixed $value , bool &$modified = false ): array|float
    {
        if( is_float( $value ) )
        {
            return $value ;
        }
        $modified = true ;
        if( is_array( $value ) )
        {
            return array_map( 'floatval' , $value ) ;
        }
        return floatval( $value ) ;
    }
}