<?php

namespace oihana\traits;

/**
 * Trait KeyValueTrait
 *
 * Provides utility methods to get and set key-value pairs from a document,
 * whether the document is an array or an object.
 *
 * This trait is useful when working with dynamic data structures
 * where the type (array or object) is not guaranteed.
 *
 * Methods:
 * - getKeyValue(): Retrieve a value by key from an array or object.
 * - setKeyValue(): Set a value by key into an array or object.
 *
 * Example usage:
 * ```php
 * use oihana\traits\KeyValueTrait;
 *
 * $data = ['name' => 'Alice'];
 * $value = $this->getKeyValue($data, 'name'); // returns 'Alice'
 *
 * $data = $this->setKeyValue($data, 'age', 30);
 * ```
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait KeyValueTrait
{
    /**
     * Gets the property value of with a specific key name in a document.
     * @param array|object $document
     * @param string $key
     * @param ?bool $isArray Set automatically the document type if null or indicates manually if the document is an array (true) or an object (false).
     * @return mixed
     */
    public function getKeyValue( array|object $document , string $key , ?bool $isArray = null ) :mixed
    {
        if( !is_bool( $isArray ) )
        {
            $isArray = is_array( $document );
        }

        return $isArray ? ( $document[ $key ] ?? null ) : ( $document->{ $key } ?? null );
    }

    /**
     * Set the property value of with a specific key name in a document.
     * @param array|object $document
     * @param string $key
     * @param mixed $value
     * @param ?bool $isArray Set automatically the document type if null or indicates manually if the document is an array (true) or an object (false).
     * @return array|object
     */
    public function setKeyValue( array|object $document , string $key , mixed $value , ?bool $isArray = null ) :array|object
    {
        if( !is_bool( $isArray ) )
        {
            $isArray = is_array( $document );
        }

        if( $isArray )
        {
            $document[ $key ] = $value ;
        }
        else
        {
            $document->{ $key } = $value ;
        }

        return $document ;
    }
}