<?php

namespace oihana\reflections;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionUnionType;

use function oihana\core\arrays\isAssociative;

class Reflection
{
    /**
     * Returns the short class name of the given object or class.
     * @param object $object
     * @return string
     */
    public function className( object $object ) :string
    {
        $class = get_class( $object ) ;
        $parts = explode('\\',  $class ) ;
        return end( $parts ) ;
    }

    /**
     * Returns an array of constants for the given object or class.
     *
     * @param object|string $class The object or class to reflect upon.
     * @param int $filter The filter to apply to the constants (default is ReflectionClassConstant::IS_PUBLIC).
     * @return array The array of constants.
     * @throws ReflectionException If the reflection class cannot be created.
     */
    public function constants( object|string $class, int $filter = ReflectionClassConstant::IS_PUBLIC ): array
    {
        return $this->reflection( $class )->getConstants( $filter );
    }

    /**
     * Hydrates an object of the specified class from an associative array of data.
     *
     * This method attempts to map array keys to public properties of the target object.
     * It supports recursive hydration for nested objects, provided that the nested
     * properties have type hints (e.g., `public ?GeoCoordinates $geo;`).
     *
     * Special handling is included for PHP 8.0+ union types (e.g., `Type|null`).
     *
     * @param array $thing An associative array containing the data to hydrate the object.
     * Keys should ideally match the public property names of the `$class`.
     * @param string $class The fully qualified class name of the object to be instantiated and hydrated (e.g., `App\Entity\User::class`).
     * This can be passed to optimize performance by avoiding re-instantiation
     * during recursive calls. If not provided, it will be created internally.
     *
     * @return object The newly instantiated and hydrated object of type `$class`.
     *
     * @throws InvalidArgumentException If the provided `$class` does not exist or is not a valid class.
     * @throws ReflectionException If a property reflection fails (e.g., due to an invalid property name, though unlikely with `hasProperty` check).
     * This is explicitly tagged as per your request, even if direct `ReflectionException`
     * throws aren't immediately visible in the provided snippet.
     */
    public function hydrate( array $thing , string $class ): object
    {
        if ( !class_exists( $class ) )
        {
            throw new InvalidArgumentException("hydrate failed, the class '{$class}' does not exist.");
        }

        $reflectionClass = $this->reflection( $class ) ;

        $object = new $class() ;

        foreach ( $thing as $key => $value )
        {
            if ( $reflectionClass->hasProperty( $key ) )
            {
                $property = $reflectionClass->getProperty( $key ) ;

                if ( $property->hasType() )
                {
                    $propertyType = $property->getType() ;
                    $types        = [] ;

                    if ( $propertyType instanceof ReflectionUnionType )
                    {
                        foreach ($propertyType->getTypes() as $type)
                        {
                            $types[] = $type ;
                        }
                    }
                    else
                    {
                        $types[] = $propertyType ;
                    }

                    foreach ( $types as $type )
                    {
                        $typeName = $type->getName() ;

                        if ( $typeName === 'null' && $value === null )
                        {
                            break;
                        }

                        if ( class_exists( $typeName ) )
                        {
                            if ( is_array($value ) )
                            {
                                if ( isAssociative( $value ) )
                                {
                                    $value = $this->hydrate( $value , $typeName ) ;
                                }
                                else {
                                    $value = array_map( fn($v) => $this->hydrate( $v , $typeName ) , $value ) ;
                                }
                            }
                            break;
                        }

                        if ( $typeName === 'array' && is_array($value) )
                        {
                            break;
                        }
                    }
                }

                if ( $property->isPublic() )
                {
                    $object->{ $key } = $value ;
                }
            }
        }

        return $object;
    }

    /**
     * Returns an array of methods for the given object or class.
     *
     * @param object|string $class The object or class to reflect upon.
     * @param int $filter The filter to apply to the methods (default is ReflectionMethod::IS_PUBLIC).
     * @return array The array of methods.
     * @throws ReflectionException If the reflection class cannot be created.
     */
    public function methods( object|string $class, int $filter = ReflectionMethod::IS_PUBLIC ) : array
    {
        return $this->reflection( $class )->getMethods( $filter );
    }

    /**
     * Returns an array of properties for the given object or class.
     * @param object|string $class The object or class to reflect upon.
     * @param int $filter The filter to apply to the properties (default is ReflectionProperty::IS_PUBLIC).
     * @return array The array of properties.
     * @throws ReflectionException If the reflection class cannot be created.
     */
    public function properties( object|string $class , int $filter = ReflectionProperty::IS_PUBLIC ): array
    {
        return $this->reflection( $class )->getProperties($filter);
    }

    /**
     * Returns the reflection class for the given object or class.
     *
     * @param object|string $class The object or class to reflect upon.
     * @return ReflectionClass The reflection class.
     * @throws ReflectionException If the reflection class cannot be created.
     */
    public function reflection( object|string $class ): ReflectionClass
    {
        $className = is_string( $class ) ? $class : $class::class;

        if ( !isset( $this->reflections[ $className ] ) )
        {
            $this->reflections[ $className ] = new ReflectionClass( $className );
        }

        return $this->reflections[ $className ];
    }

    /**
     * Returns the class short name.
     * @param object|string $class The object or class to reflect upon.
     * @return string
     * @throws ReflectionException If the reflection class cannot be created.
     */
    public function shortName( object|string $class ): string
    {
        return $this->reflection( $class )->getShortName();
    }

    /**
     * @var array
     */
    protected array $reflections = [] ;
}