<?php

namespace oihana\reflections\traits ;

use ReflectionException;
use ReflectionProperty;

use oihana\reflections\Reflection;

use function oihana\core\arrays\compress;

trait ReflectionTrait
{
    private ?Reflection $__reflection = null ;
    private ?string     $__shortName  = null ;

    /**
     * The internal Reflection reference.
     * @var ?Reflection
     */
    protected ?Reflection $reflection
    {
        get => $this->__reflection ??= new Reflection() ;
    }

    /**
     * Returns the list of all constants of the given object/class.
     *
     * @param object|string $class The object or the classname reference.
     * @return array
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class MyConstants {
     *     public const FOO = 'bar';
     * }
     * $trait->getConstants(MyConstants::class); // ['FOO' => 'bar']
     * ```
     */
    public function getConstants( object|string $class ) : array
    {
        return $this->reflection->constants($class);
    }

    /**
     * Internal methods to get the public
     *
     * @param object|string $class The object or the classname reference.
     * @return ReflectionProperty[] An array of reflection property objects.
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class User {
     *     public string $name;
     * }
     * $trait->getPublicProperties(User::class); // [ReflectionProperty{name: 'name'}]
     * ```
     */
    public function getPublicProperties( object|string $class ) : array
    {
        return $this->reflection->properties($class);
    }

    /**
     * Returns the class short name.
     *
     * @param object|string $class The object or the classname reference.
     * @return string
     * @throws ReflectionException
     *
     * @example
     * ```php
     * $trait->getShortName(\App\Models\User::class); // 'User'
     * ```
     */
    public function getShortName( object|string $class ) : string
    {
        return $this->__shortName ??= $this->reflection->shortName($class);
    }

    /**
     * Returns the list of method parameters as ReflectionParameter[].
     *
     * @param object|string $class
     * @param string        $method
     * @return array
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(string $x, int $y = 0) {} }
     * $trait->getMethodParameters(Foo::class, 'bar');
     * // [ReflectionParameter{name: 'x'}, ReflectionParameter{name: 'y'}]
     * ```
     */
    public function getMethodParameters( object|string $class, string $method ): array
    {
        return $this->reflection->parameters($class, $method);
    }

    /**
     * Returns the type name of the given parameter in a method, or null.
     *
     * @param object|string $class
     * @param string        $method
     * @param string        $param
     * @return string|null
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(string $x) {} }
     * $trait->getParameterType(Foo::class, 'bar', 'x'); // 'string'
     * ```
     */
    public function getParameterType(object|string $class, string $method, string $param): ?string
    {
        return $this->reflection->parameterType($class, $method, $param);
    }

    /**
     * Returns the default value of the given parameter in a method, or null.
     *
     * @param object|string $class
     * @param string        $method
     * @param string        $param
     * @return mixed|null
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(string $x = 'abc') {} }
     * $trait->getParameterDefaultValue(Foo::class, 'bar', 'x'); // 'abc'
     * ```
     */
    public function getParameterDefaultValue(object|string $class, string $method, string $param): mixed
    {
        return $this->reflection->parameterDefaultValue($class, $method, $param);
    }

    /**
     * Returns true if the given method has the specified parameter.
     *
     * @param object|string $class
     * @param string        $method
     * @param string        $param
     * @return bool
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(string $x) {} }
     * $trait->hasParameter(Foo::class, 'bar', 'x'); // true
     * $trait->hasParameter(Foo::class, 'bar', 'z'); // false
     * ```
     */
    public function hasParameter(object|string $class, string $method, string $param): bool
    {
        return $this->reflection->hasParameter($class, $method, $param);
    }

    /**
     * Returns true if the given parameter in a method is nullable.
     *
     * @param object|string $class
     * @param string        $method
     * @param string        $param
     * @return bool
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(?string $x) {} }
     * $trait->isParameterNullable(Foo::class, 'bar', 'x'); // true
     * ```
     */
    public function isParameterNullable(object|string $class, string $method, string $param): bool
    {
        return $this->reflection->isParameterNullable($class, $method, $param);
    }

    /**
     * Returns true if the given parameter in a method is optional.
     *
     * @param object|string $class
     * @param string        $method
     * @param string        $param
     * @return bool
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(string $x = 'abc') {} }
     * $trait->isParameterOptional(Foo::class, 'bar', 'x'); // true
     * ```
     */
    public function isParameterOptional(object|string $class, string $method, string $param): bool
    {
        return $this->reflection->isParameterOptional($class, $method, $param);
    }

    /**
     * Returns true if the given parameter in a method is variadic.
     *
     * @param object|string $class
     * @param string        $method
     * @param string        $param
     * @return bool
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Foo { public function bar(string ...$x) {} }
     * $trait->isParameterVariadic(Foo::class, 'bar', 'x'); // true
     * ```
     */
    public function isParameterVariadic(object|string $class, string $method, string $param): bool
    {
        return $this->reflection->isParameterVariadic($class, $method, $param);
    }

    /**
     * Populates an object of the given class with data from the provided array.
     *
     * @param array $thing The data array used to hydrate the object.
     * @param string $class The classname of the object to be hydrated.
     * @return object The hydrated object of the given class.
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class User { public string $name; }
     * $trait->hydrate(['name' => 'Alice'], User::class); // User{name: 'Alice'}
     * ```
     */
    public function hydrate( array $thing , string $class ): object
    {
        return $this->reflection->hydrate($thing, $class);
    }

    /**
     * Invoked to generates the json array serializer array representation from the public properties of the object.
     *
     * @param object|string $class The classname of the object to be serialized.
     * @param bool $reduce Indicates if the returned associative array is compressed, the null properties are removed.
     * @return array
     * @throws ReflectionException
     *
     * @example
     * ```php
     * class Product { public string $name = 'Book'; public ?string $desc = null; }
     * $trait->jsonSerializeFromPublicProperties(Product::class); // ['name' => 'Book', 'desc' => null]
     * $trait->jsonSerializeFromPublicProperties(Product::class, true); // ['name' => 'Book']
     * ```
     */
    public function jsonSerializeFromPublicProperties( object|string $class , bool $reduce = false ):array
    {
        $object     = [] ;
        $properties = $this->getPublicProperties( $class ) ;

        foreach( $properties as $property )
        {
            $name = $property->getName();

            $object[ $name ] = $this->{ $name } ?? null ;
        }

        return $reduce ? compress( $object ) : $object ;
    }
}