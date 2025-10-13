<?php

namespace oihana\traits;

use DI\DependencyException;
use DI\NotFoundException;

use oihana\models\enums\ModelParam;
use oihana\traits\alters\AlterValueTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\enums\Alter;
use oihana\traits\alters\AlterArrayCleanPropertyTrait;
use oihana\traits\alters\AlterArrayPropertyTrait;
use oihana\traits\alters\AlterCallablePropertyTrait;
use oihana\traits\alters\AlterFloatPropertyTrait;
use oihana\traits\alters\AlterGetDocumentPropertyTrait;
use oihana\traits\alters\AlterIntPropertyTrait;
use oihana\traits\alters\AlterJSONParsePropertyTrait;
use oihana\traits\alters\AlterJSONStringifyPropertyTrait;
use oihana\traits\alters\AlterUrlPropertyTrait;

use function oihana\core\accessors\getKeyValue;
use function oihana\core\accessors\setKeyValue;
use function oihana\core\arrays\isAssociative;

/**
 * Provides a system to alter properties of arrays or objects based on a configurable set of rules (called "alters").
 *
 * The alteration definitions are stored in the `$alters` array and can apply various transformations to data,
 * such as converting values to floats, parsing JSON, cleaning arrays, or resolving URLs.
 *
 * Example usage:
 * ```php
 * class MyProcessor
 * {
 *     use AlterDocumentTrait;
 *
 *     public function __construct()
 *     {
 *         $this->alters =
 *         [
 *             'price' => Alter::FLOAT,
 *             'tags'  => [ Alter::ARRAY , Alter::CLEAN ],
 *             'meta'  => [ Alter::JSON_PARSE ],
 *             'link'  => [ Alter::URL , '/product/' ],
 *             'score' => [ Alter::CALL , fn($value) => $value * 10 ],
 *         ];
 *     }
 * }
 *
 * $processor = new MyProcessor();
 * $data = [
 * 'price' => '12.5',
 * 'tags'  => 'tag1;;tag2;',
 * 'meta'  => '{"views":100}',
 * 'link'  => '123',
 * 'score' => 7,
 * ];
 *
 * $processed = $processor->alter($data);
 * // Result:
 * // [
 * //     'price' => 12.5,
 * //     'tags'  => ['tag1', 'tag2'],
 * //     'meta'  => ['views' => 100],
 * //     'link'  => '/product/123',
 * //     'score' => 70,
 * // ]
 * ```
 *
 * Supported alteration types (see enum Alter):
 * - Alter::ARRAY           → Split string into array and apply sub-alters.
 * - Alter::CLEAN           → Remove empty/null elements from an array.
 * - Alter::CALL            → Call a function on the value.
 * - Alter::FLOAT           → Convert to float (or array of floats).
 * - Alter::GET             → Fetch a document using a model.
 * - Alter::INT             → Convert to integer (or array of integers).
 * - Alter::JSON_PARSE      → Parse JSON string.
 * - Alter::JSON_STRINGIFY  → Convert value to JSON string.
 * - Alter::URL             → Generate a URL from a property.
 * - Alter::VALUE           → Override with a fixed value.
 *
 * @param mixed $document The array or object to transform.
 * If it's a list of objects/arrays, the alteration is recursively applied to each.
 *
 * @return mixed The altered version of the document (same type as input).
 *
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 * @throws DependencyException
 * @throws NotFoundException
 *
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait AlterDocumentTrait
{
    use AlterArrayPropertyTrait ,
        AlterArrayCleanPropertyTrait ,
        AlterCallablePropertyTrait ,
        AlterFloatPropertyTrait ,
        AlterGetDocumentPropertyTrait ,
        AlterIntPropertyTrait ,
        AlterJSONParsePropertyTrait ,
        AlterJSONStringifyPropertyTrait ,
        AlterUrlPropertyTrait ,
        AlterValueTrait ,
        KeyValueTrait
        ;

    /**
     * The enumeration of all definitions to alter on the array or object key/value properties.
     */
    public array $alters = [] ;

    /**
     * Alters the given document (array or object) based on the configured `$alters` definitions.
     *
     * This method determines the structure of the document and applies the appropriate transformation logic:
     * - If the document is an **associative array**, each key listed in `$alters` is processed using `alterAssociativeArray()`.
     * - If the document is a **sequential array** (i.e., a list of items), the alteration is recursively applied to each item.
     * - If the document is an **object**, its public properties are altered using `alterObject()`.
     * - If `$alters` is empty or no matching keys/properties are found, the document is returned unchanged.
     *
     * @param mixed $document The input to alter. Can be an associative array, object, or a list of items.
     *
     * @return mixed The altered document, same structure as input.
     *
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     *
     * @example
     * ```php
     * class Example
     * {
     *     use AlterDocumentTrait;
     *
     *     public function __construct()
     *     {
     *         $this->alters =
     *         [
     *            'price' => Alter::FLOAT,
     *            'tags'  => [ Alter::ARRAY , Alter::CLEAN ],
     *         ];
     *     }
     * }
     *
     * $input = [ 'price' => '19.99', 'tags'  => 'foo,bar' ];
     *
     * $processor = new Example();
     * $output = $processor->alter($input);
     *
     * // $output:
     * // [
     * //     'price' => 19.99,
     * //     'tags'  => ['foo', 'bar'],
     * // ]
     * ```
     */
    public function alter( mixed $document ) :mixed
    {
        if( count( $this->alters ) > 0 )
        {
            if( is_array( $document ) )
            {
                if( isAssociative( $document ) )
                {
                    return $this->alterAssociativeArray( $document ) ;
                }
                else
                {
                    return array_map( fn( $value ) => $this->alter( $value ) , $document ) ;
                }
            }
            elseif( is_object( $document ) )
            {
                return $this->alterObject( $document ) ;
            }
        }
        return $document ;
    }

    /**
     * Alter the passed-in array.
     * @param array $document
     * @return array
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function alterAssociativeArray( array $document ) : array
    {
        foreach( $this->alters as $key => $definition )
        {
            if( array_key_exists( $key , $document ) )
            {
                $document = $this->alterProperty( $key , $document , $definition , true ) ;
            }
        }
        return $document ;
    }

    /**
     * Alter the passed-in object.
     * @param object $document
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     */
    public function alterObject( object $document ) :object
    {
        foreach( $this->alters as $key => $definition )
        {
            if( property_exists( $document, $key ) )
            {
                $document = $this->alterProperty( $key , $document , $definition , false ) ;
            }
        }
        return $document ;
    }

    /**
     * Alters a specific property of the given document (array or object) using a defined transformation rule.
     *
     * The transformation is defined by the `$definition` argument, which can be either:
     * - A string representing a single `Alter::` constant (e.g. `Alter::FLOAT`)
     * - An array, where the first element is an `Alter::` constant and the rest are parameters for that transformation
     *
     * If the alteration modifies the value, the altered value is set back into the document.
     * Otherwise, the original document is returned unmodified.
     *
     * Supported alter types:
     * - Alter::ARRAY          — Explodes a string into an array (using `;`) and applies sub-alters
     * - Alter::CALL           — Calls a user-defined callable on the value
     * - Alter::CLEAN          — Removes empty (`""`) or null elements from arrays
     * - Alter::FLOAT          — Casts the value to float
     * - Alter::GET            — Resolves a document by ID using a model
     * - Alter::INT            — Casts the value to integer
     * - Alter::JSON_PARSE     — Parses a JSON string into a PHP value
     * - Alter::JSON_STRINGIFY — Encodes a value into a JSON string
     * - Alter::URL            — Generates a URL based on document properties
     * - Alter::VALUE          — Replaces the value with a fixed constant
     *
     * @param string       $key        The name of the property to alter (e.g. 'price', 'tags')
     * @param array|object $document   The document (array or object) passed by reference
     * @param string|array $definition The alteration definition: either a string (`Alter::`) or an array (`[ Alter::X , ...args ]`)
     * @param ?bool        $isArray    Optional flag to indicate the type of document. If null, it will be inferred automatically.
     *
     * @return array|object The altered document (same reference type as input)
     *
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @example
     * ```php
     * $this->alters =
     * [
     *     'price'    => Alter::FLOAT,                         // Casts 'price' to float
     *     'tags'     => [ Alter::ARRAY , Alter::CLEAN ],      // Splits and cleans 'tags'
     *     'meta'     => [ Alter::JSON_PARSE ],                // Parses 'meta' JSON string
     *     'url'      => [ Alter::URL , '/product/' ],         // Generates a product URL
     *     'discount' => [ Alter::CALL , fn($v) => $v * 0.9 ], // Applies a callable
     *     'rating'   => [ Alter::VALUE , 5 ]                  // Fixed value replacement
     * ];
     *
     * $document =
     * [
     *     'price'    => '29.90',
     *     'tags'     => 'a;;b;',
     *     'meta'     => '{"active":true}',
     *     'url'      => '123',
     *     'discount' => 100,
     *     'rating'   => 0,
     * ];
     *
     * $result = $this->alterProperty('price', $document, Alter::FLOAT, true);
     * // Returns the document with 'price' casted to float (29.9)
     * ```
     */
    public function alterProperty( string $key , array|object $document , string|array $definition , ?bool $isArray = null ) : array|object
    {
        if( is_array( $definition ) && count( $definition ) > 0 )
        {
            $alter = array_shift( $definition ) ;
        }
        else
        {
            $alter = $definition ;
            $definition = [] ;
        }

        $value    = getKeyValue( document: $document , key: $key , isArray: $isArray ) ;
        $modified = false ;
        $value    = match ( $alter )
        {
            Alter::ARRAY          => $this->alterArrayProperty( $value , $definition , $modified ) ,
            Alter::CALL           => $this->alterCallableProperty( $value , $definition , $modified ) ,
            Alter::CLEAN          => $this->alterArrayCleanProperty( $value , $modified ),
            Alter::FLOAT          => $this->alterFloatProperty( $value , $modified ),
            Alter::GET            => $this->alterGetDocument( $value , $definition , $modified ) ,
            Alter::INT            => $this->alterIntProperty( $value , $modified ) ,
            Alter::JSON_PARSE     => $this->alterJsonParseProperty( $value , $definition , $modified ) ,
            Alter::JSON_STRINGIFY => $this->alterJsonStringifyProperty( $value , $definition , $modified ),
            Alter::URL            => $this->alterUrlProperty( $document , $definition , $isArray , $modified ),
            Alter::VALUE          => $this->alterValue( $value , $definition[0] ?? null, $modified ) ,
            default               => $value,
        };

        return $modified ? setKeyValue( $document , $key , $value , isArray: $isArray ) : $document;
    }

    /**
     * Initialize the 'alters' property.
     * @param array $init
     * @return static
     */
    public function initializeAlters( array $init = [] ):static
    {
        $this->alters = $init[ ModelParam::ALTERS ] ?? $this->alters ;
        return $this ;
    }
}