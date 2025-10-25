<?php

namespace oihana\models\traits\alters;

use oihana\reflect\traits\ReflectionTrait;
use org\schema\Thing;

use ReflectionException;

trait AlterHydratePropertyTrait
{
    use ReflectionTrait ;

    /**
     * Cast a value to custom class. If the value is an array, all elements in the array are casted.
     *
     * ```
     * Property::GEO => [ Alter::HYDRATE , GeoCoordinates::class ] ,
     * ```
     * @param mixed $value      The original value to alter.
     * @param array $definition The definition reference to extract the schema to apply.
     * @param bool $modified    Will be set to true if the value was replaced
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function alterHydrateProperty
    (
        mixed $value ,
        array $definition = [] ,
        bool  &$modified  = false
    )
    : mixed
    {
        if ( !is_array( $value ) )
        {
            return $value ;
        }

        $newValue = $value ;
        $schema   = $definition[0] ?? null ;

        if( is_string( $schema ) && class_exists( $schema ) )
        {
            if ( is_a( $schema , Thing::class , true ) )
            {
                $newValue = new $schema( $value ) ;
            }
            else
            {
                $newValue = $this->hydrate( $value , $schema ) ;
            }
        }

        $modified = $value !== $newValue ;

        return $newValue ;
    }
}