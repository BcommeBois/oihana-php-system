<?php

namespace oihana\reflections;

use InvalidArgumentException;

use ReflectionClass;
use ReflectionClassConstant;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use ReflectionUnionType;

use oihana\reflections\attributes\HydrateAs;
use oihana\reflections\attributes\HydrateKey;
use oihana\reflections\attributes\HydrateWith;

use function oihana\core\arrays\isAssociative;

class Reflection
{
    /**
     * Returns the short class name (without namespace) of the given object.
     *
     * @param object $object The object to reflect.
     * @return string The short class name.
     *
     * @example
     * ```php
     * echo (new Reflection())->className(new \App\Entity\User());
     * // Output: 'User'
     * ```
     */
    public function className( object $object ) :string
    {
        $class = get_class( $object ) ;
        $parts = explode('\\',  $class ) ;
        return end( $parts ) ;
    }

    /**
     * Returns an array of constants defined in the given class.
     *
     * @param object|string $class The object or class name.
     * @param int $filter A bitmask of constant visibility (default: public).
     * @return array<string, mixed> Associative array of constant names and values.
     * @throws ReflectionException If reflection fails.
     *
     * @example
     * ```php
     * class MyStatus {
     *     public const ACTIVE = 'active';
     *     private const SECRET = 'hidden';
     * }
     *
     * $constants = (new Reflection())->constants(MyStatus::class);
     * print_r($constants);
     * // Output: ['ACTIVE' => 'active']
     * ```
     */
    public function constants( object|string $class, int $filter = ReflectionClassConstant::IS_PUBLIC ): array
    {
        return $this->reflection( $class )->getConstants( $filter );
    }

    /**
     * Instantiates and hydrates an object of a given class using associative array data.
     *
     * It supports:
     * - Recursive hydration for nested objects (flat or array),
     * - Union types (e.g., `Type|null`),
     * - Custom source keys with `#[HydrateKey]`,
     * - Array hydration via `#[HydrateWith]`, `#[HydrateAs]`, or PHPDoc `@ var Type[]`,
     * - Public properties only (private/protected are ignored).
     *
     * @param array  $thing Associative array of data (keys must match public properties or be aliased via attributes).
     * @param string $class Fully qualified class name of the object to instantiate.
     *
     * @return object The hydrated object instance.
     *
     * @throws InvalidArgumentException If the class does not exist or required non-nullable property is null.
     * @throws ReflectionException If property introspection fails.
     *
     * @example Flat object hydration
     * ```php
     * class User {
     *     public string $name;
     * }
     *
     * $data = ['name' => 'Alice'];
     * $user = (new Reflection())->hydrate($data, User::class);
     * echo $user->name; // "Alice"
     * ```
     *
     * @example Nested object hydration
     * ```php
     * class Address {
     *     public string $city;
     * }
     * class User {
     *     public string $name;
     *     public ?Address $address = null;
     * }
     *
     * $data = ['name' => 'Alice', 'address' => ['city' => 'Paris']];
     * $user = (new Reflection())->hydrate($data, User::class);
     * echo $user->address->city; // "Paris"
     * ```
     *
     * @example Hydration with `#[HydrateKey]`
     * ```php
     * use oihana\reflections\attributes\HydrateKey;
     *
     * class User {
     *     #[HydrateKey('user_name')]
     *     public string $name;
     * }
     *
     * $data = ['user_name' => 'Bob'];
     * $user = (new Reflection())->hydrate($data, User::class);
     * echo $user->name; // "Bob"
     * ```
     *
     * @example Hydration of array of objects via `#[HydrateWith]`
     * ```php
     * use oihana\reflections\attributes\HydrateWith;
     *
     * class Address {
     *     public string $city;
     * }
     * class Geo {
     *     #[HydrateWith(Address::class)]
     *     public array $locations = [];
     * }
     *
     * $data = ['locations' => [['city' => 'Paris'], ['city' => 'Berlin']]];
     * $geo = (new Reflection())->hydrate($data, Geo::class);
     * echo $geo->locations[1]->city; // "Berlin"
     * ```
     *
     * @example Hydration of array via `@var Type[]`
     * ```php
     * class Address
     * {
     *     public string $city;
     * }
     *
     * class Geo
     * {
     *     / ** @ var Address[] * /
     *     public array $locations = [];
     * }
     *
     * $data = ['locations' => [['city' => 'Lyon'], ['city' => 'Nice']]];
     * $geo = (new Reflection())->hydrate($data, Geo::class);
     * echo $geo->locations[0]->city; // "Lyon"
     * ```
     *
     * @example Hydration of array via `@var array<Address>`
     * ```php
     * class Address
     * {
     *     public string $city;
     * }
     *
     * class Geo
     * {
     *     / ** @ var array<Address> * /
     *     public array $locations = [];
     * }
     * ```
     *
     * @example Union types
     * ```php
     * class Profile {
     *     public ?string $bio = null;
     * }
     *
     * $data = ['bio' => null];
     * $profile = ( new Reflection() )->hydrate( $data , Profile::class ) ;
     * var_dump($profile->bio); // null
     * ```
     */
    public function hydrate( array $thing , string $class ): object
    {
        if ( !class_exists( $class ) )
        {
            throw new InvalidArgumentException("hydrate failed, the class '$class' does not exist.");
        }

        $reflectionClass = $this->reflection( $class ) ;
        $object          = new $class() ;
        $properties      = $reflectionClass->getProperties() ;

        foreach ( $properties as $property)
        {
            // Determines the key to be used in $thing (via #[HydrateKey])
            $propertyKey = $property->getName() ;
            $keyAttr     = $property->getAttributes(HydrateKey::class ) ;

            if ( !empty( $keyAttr ) )
            {
                $propertyKey = $keyAttr[0]->newInstance()->key ;
            }

            if ( !array_key_exists( $propertyKey , $thing ) )
            {
                continue;
            }

            $value = $thing[ $propertyKey ] ;

            if ( $property->hasType() )
            {
                $propertyType = $property->getType() ;

                $types = $propertyType instanceof ReflectionUnionType
                       ? $propertyType->getTypes()
                       : [ $propertyType ] ;

                $hydrated = false ;

                foreach ( $types as $type )
                {
                    $typeName = $type->getName();

                    if ( $typeName === 'null' && $value === null )
                    {
                        break;
                    }

                    // Attribut #[HydrateAs(Foo::class)]
                    $hydrateAs = $property->getAttributes(HydrateAs::class ) ;
                    if ( !empty( $hydrateAs ) )
                    {
                        $typeName = $hydrateAs[0]->newInstance()->class ;
                    }

                    if ( $typeName === 'array' && is_array( $value ) )
                    {
                        // 1. #[HydrateWith(MyClass::class, AnotherClass::class)]
                        $hydrateWith = $property->getAttributes(HydrateWith::class ) ;
                        if ( !empty( $hydrateWith ) )
                        {
                            $possibleClasses = $hydrateWith[0]->newInstance()->classes ;
                            $hydratedArray   = [];

                            foreach ( $value as $item )
                            {
                                if ( is_array( $item ) )
                                {
                                    $itemClass = $this->determineArrayItemType($item, $possibleClasses) ;
                                    if ( $itemClass && class_exists( $itemClass ) )
                                    {
                                        $hydratedArray[] = $this->hydrate($item, $itemClass);
                                    }
                                    else
                                    {
                                        $hydratedArray[] = $item ; // Do nothing
                                    }
                                }
                                else
                                {
                                    $hydratedArray[] = $item ;
                                }
                            }

                            $value    = $hydratedArray;
                            $hydrated = true;
                            break;
                        }

                        // 2. DocComment analysis: @var MyClass | @var \oihana\package\MyClass | @var array<MockAddress>
                        $doc = $property->getDocComment() ;
                        if
                        (
                            $doc &&
                            preg_match('/@var\s+((\w+(?:\\\\\w+)*)\[\]|array<(\w+(?:\\\\\w+)*)>)/' , $doc , $matches )
                        )
                        {
                            $itemClass = $matches[1] ?: $matches[2]; // Support both Type[] and array<Type>
                            if ( class_exists( $itemClass ) )
                            {
                                $value    = array_map( fn( $v ) => is_array( $v ) ? $this->hydrate( $v , $itemClass ) : $v , $value );
                                $hydrated = true;
                                break;
                            }
                        }
                    }
                    else if ( class_exists( $typeName ) )
                    {
                        if ( is_array( $value ) )
                        {
                            $value = isAssociative( $value )
                                   ? $this->hydrate( $value , $typeName )
                                   : array_map( fn( $v ) => $this->hydrate( $v , $typeName ) , $value ) ;
                            $hydrated = true ;
                            break;
                        }
                    }
                }

                if ( !$hydrated && $value === null && !$property->getType()->allowsNull() )
                {
                    throw new InvalidArgumentException("Property {$property->getName()} does not allow null" ) ;
                }
            }

            if ( $property->isPublic() )
            {
                $object->{ $property->getName() } = $value;
            }
        }

        return $object;
    }

    /**
     * Returns an array of methods for the given class or object.
     *
     * @param object|string $class The object or class name.
     * @param int $filter Method visibility filter (default: public).
     * @return array<int, ReflectionMethod> Array of reflection method objects.
     * @throws ReflectionException If reflection fails.
     *
     * @example
     * ```php
     * class MyClass
     * {
     *     public function foo() {}
     *     protected function bar() {}
     * }
     *
     * $methods = (new Reflection())->methods(MyClass::class);
     * foreach ($methods as $method)
     * {
     *     echo $method->getName(); // 'foo'
     * }
     * ```
     */
    public function methods( object|string $class, int $filter = ReflectionMethod::IS_PUBLIC ) : array
    {
        return $this->reflection( $class )->getMethods( $filter );
    }

    /**
     * Returns an array of properties for the given class or object.
     *
     * @param object|string $class The object or class name.
     * @param int $filter Property visibility filter (default: public).
     * @return ReflectionProperty[] An array of reflection property objects.
     * @throws ReflectionException If reflection fails.
     *
     * @example
     * ```php
     * class Item {
     *     public string $name;
     *     private int $id;
     * }
     * $props = (new Reflection())->properties(Item::class);
     * foreach ($props as $prop) {
     *     echo $prop->getName(); // 'name'
     * }
     * ```
     */
    public function properties( object|string $class , int $filter = ReflectionProperty::IS_PUBLIC ): array
    {
        return $this->reflection( $class )->getProperties( $filter ) ;
    }

    /**
     * Returns a cached ReflectionClass instance for the given class or object.
     *
     * @param object|string $class The object or class name.
     * @return ReflectionClass The reflection class.
     * @throws ReflectionException If reflection fails.
     *
     * @example
     * ```php
     * $reflectionClass = (new Reflection())->reflection(\App\Entity\User::class);
     * echo $reflectionClass->getName(); // 'App\Entity\User'
     * ```
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
     * Returns the short (unqualified) name of the class.
     *
     * @param object|string $class The object or class name.
     * @return string The short name of the class.
     * @throws ReflectionException If reflection fails.
     *
     * @example
     * ```php
     * echo (new Reflection())->shortName(\App\Models\Product::class);
     * // Output: 'Product'
     * ```
     */
    public function shortName( object|string $class ): string
    {
        return $this->reflection( $class )->getShortName();
    }

    /**
     * Internal cache of reflection instances.
     * @var array<string, ReflectionClass>
     */
    protected array $reflections = [] ;

    /**
     * Determine the type of an array element
     * @param $item
     * @param array $possibleClasses
     * @return string|null
     * @throws ReflectionException
     */
    private function determineArrayItemType($item, array $possibleClasses): ?string
    {
        if (!is_array($item)) {
            return null;
        }

        // Strategy 1: Search for a discriminator (field ‘type’, ‘@type’, etc.)
        if ( isset( $item[ '@type' ] ) )
        {
            $type = $item['@type'] ;
            foreach ( $possibleClasses as $class )
            {
                if ( $this->shortName( $class ) === $type || $class === $type )
                {
                    return $class ;
                }
            }
        }

        if ( isset($item['type'] ) )
        {
            $type = $item['type'];
            foreach ($possibleClasses as $class)
            {
                if ( strcasecmp( $this->shortName( $class ) , $type ) === 0 )
                {
                    return $class;
                }
            }
        }

        // Strategy 2: Analyze properties to guess the type
        return $this->guessClassFromProperties($item, $possibleClasses);
    }

    /**
     * Attempts to guess the most appropriate class from a list of possible classes
     * based on the presence of matching properties in the provided input array.
     *
     * The score is computed by checking if each class property (or its alternative
     * key defined by a `HydrateKey` attribute) exists in the `$item` array. The class
     * with the highest normalized score (above 0.3) is returned.
     *
     * If no class scores high enough, the first class in the `$possibleClasses` list is
     * returned as fallback (if provided), otherwise `null`.
     *
     * @param array $item             The associative array of input data to match against class properties.
     * @param array $possibleClasses  A list of fully qualified class names to consider.
     *
     * @return string|null            The best matching class name or `null` if none found.
     *
     * @throws ReflectionException   If a class cannot be reflected upon.
     *
     * @example
     * ```php
     * class User
     * {
     *     #[HydrateKey('user_id')]
     *     public string $id;
     *     public string $name;
     * }
     *
     * class Product
     * {
     *     public string $sku;
     *     public string $name;
     * }
     *
     * $item = ['user_id' => '123', 'name' => 'Alice'];
     * $guessedClass = $this->guessClassFromProperties($item, [User::class, Product::class]);
     *
     * echo $guessedClass; // Outputs: "User"
     * ```
     */
    private function guessClassFromProperties( array $item , array $possibleClasses ): ?string
    {
        $maxScore  = 0;
        $bestMatch = null;

        foreach ( $possibleClasses as $class )
        {
            if ( !class_exists( $class ) )
            {
                continue ;
            }

            $score      = 0 ;
            $properties = $this->properties( $class );
            $totalProps = count( $properties ) ;

            foreach ( $properties as $property )
            {
                $propertyName = $property->getName();

                if ( array_key_exists( $propertyName , $item ) )
                {
                    $score += 2 ; // Bonus for existing property
                }

                // Vérifier les attributs HydrateKey
                $keyAttr = $property->getAttributes(HydrateKey::class ) ;
                if ( !empty( $keyAttr ) )
                {
                    $alternativeKey = $keyAttr[0]->newInstance()->key ;
                    if ( array_key_exists( $alternativeKey , $item ) )
                    {
                        $score += 2 ;
                    }
                }
            }

            // Calculate normalized score
            $normalizedScore = $totalProps > 0 ? ($score / ($totalProps * 2)) : 0 ;

            if ($normalizedScore > $maxScore)
            {
                $maxScore = $normalizedScore;
                $bestMatch = $class;
            }
        }

        // Return the best match if the score is sufficient
        return $maxScore > 0.3 ? $bestMatch : $possibleClasses[0] ?? null;
    }
}