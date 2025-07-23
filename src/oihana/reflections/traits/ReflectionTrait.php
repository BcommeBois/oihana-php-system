<?php

namespace oihana\reflections\traits ;

use ReflectionException;
use ReflectionProperty;

use oihana\reflections\Reflection;

use function oihana\core\arrays\compress;

trait ReflectionTrait
{
    /**
     * @var string|null
     */
    private ?string $__shortName = null ;

    /**
     * @var ?Reflection
     */
    private ?Reflection $__reflection = null ;

    /**
     * Returns the list of all constants of the given object/class.
     * @param object|string $class The object or the classname reference.
     * @return array
     * @throws ReflectionException
     */
    public function getConstants( object|string $class ) : array
    {
        if( !isset( $this->__reflection ) )
        {
            $this->__reflection = new Reflection() ;
        }
        return $this->__reflection->constants( $class ) ;
    }

    /**
     * Internal methods to get the public
     * @param object|string $class The object or the classname reference.
     * @return ReflectionProperty[] An array of reflection property objects.
     * @throws ReflectionException
     */
    public function getPublicProperties( object|string $class ) : array
    {
        if( !isset( $this->__reflection ) )
        {
            $this->__reflection = new Reflection() ;
        }
        return $this->__reflection->properties( $class ) ;
    }

    /**
     * Returns the class short name.
     * @param object|string $class The object or the classname reference.
     * @return string
     * @throws ReflectionException
     */
    public function getShortName( object|string $class ) : string
    {
        if( !isset( $this->shortName ) )
        {
            if( !isset( $this->__reflection ) )
            {
                $this->__reflection = new Reflection() ;
            }
            $this->__shortName = $this->__reflection->shortName( $class ) ;
        }

        return $this->__shortName ;
    }

    /**
     * Populates an object of the given class with data from the provided array.
     * @param array $thing The data array used to hydrate the object.
     * @param string $class The classname of the object to be hydrated.
     * @return object The hydrated object of the given class.
     * @throws ReflectionException
     */
    public function hydrate( array $thing , string $class ): object
    {
        if( !isset( $this->__reflection ) )
        {
            $this->__reflection = new Reflection() ;
        }
        return $this->__reflection->hydrate( $thing , $class ) ;
    }

    /**
     * Invoked to generates the json array serializer array representation from the public properties of the object.
     * @param object|string $class The classname of the object to be serialized.
     * @param bool $reduce Indicates if the returned associative array is compressed, the null properties are removed.
     * @return array
     * @throws ReflectionException
     */
    public function jsonSerializeFromPublicProperties( object|string $class , bool $reduce = false ):array
    {
        $object = [] ;

        $properties = $this->getPublicProperties( $class ) ;
        foreach( $properties as $property )
        {
            $name = $property->getName();
            $object[ $name ] = $this->{ $name } ?? null ;
        }

        return $reduce ? compress( $object ) : $object ;
    }
}