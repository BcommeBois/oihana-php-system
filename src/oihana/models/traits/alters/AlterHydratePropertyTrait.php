<?php

namespace oihana\models\traits\alters;

use oihana\core\arrays\CleanFlag;
use oihana\reflect\traits\ReflectionTrait;
use org\schema\Thing;

use ReflectionException;
use function oihana\core\normalize;

trait AlterHydratePropertyTrait
{
    use ReflectionTrait ;

    /**
     * Cast a value to custom class. If the value is an array, all elements in the array are casted.
     *
     * ### Usage
     *  ```
     *  Property::GEO => [ Alter::HYDRATE , GeoCoordinates::class ] ,
     *  ```
     *
     * ### Note
     * If the value is an empty array, by default returns null.
     *
     * Use the {@see normalize()} function inside the method with the default flag CleanFlag::DEFAULT | CleanFlag::RETURN_NULL.
     * You can change the normalize flag with a custom flag in the third entry in the alter definition.
     *
     * Example : ```[ Alter::HYDRATE , GeoCoordinates::class , CleanFlag::NONE ]```
     *
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

        $schema = $definition[0] ?? null ;
        $flags  = $definition[1] ?? ( CleanFlag::DEFAULT | CleanFlag::RETURN_NULL ) ;

        $newValue = normalize( $value , $flags ) ;

        if( $newValue == null )
        {
            $modified = true ;
            return $newValue ;
        }

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