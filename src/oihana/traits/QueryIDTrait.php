<?php

namespace oihana\traits;

use oihana\enums\Char;

/**
 * Provides a consistent mechanism for managing an internal query identifier (`queryId`),
 * which can be used in dynamically constructed queries (e.g., AQL), log tracing, or caching.
 *
 * This trait:
 * - Stores a private query identifier string
 * - Allows programmatic access to get or set the query ID
 * - Automatically generates a default ID if none is provided
 * - Supports initialization via associative arrays (e.g., input parameters or config)
 *
 * @example
 * ```php
 * use oihana\traits\QueryIDTrait;
 *
 * class MyQueryBuilder
 * {
 *    use QueryIDTrait;
 *
 *    public function __construct(array $options = [])
 *    {
 *        $this->setQueryID($options);
 *    }
 * }
 *
 * $builder = new MyQueryBuilder(['queryId' => 'custom_query']);
 * echo $builder->getQueryID(); // 'custom_query'
 *
 * $builder->setQueryID(null);
 * echo $builder->getQueryID(); // 'query_238472' (random 6-digit suffix)
 * ```
 *
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait QueryIDTrait
{
    /**
     * The internal query identifier.
     * This property holds the unique identifier string used internally
     * for query referencing, debugging, or mapping purposes.
     * @var string
     */
    protected string $queryId ;

    /**
     * The 'query' parameter constant.
     */
    public const string QUERY = 'query' ;

    /**
     * The 'queryId' parameter constant.
     */
    public const string QUERY_ID = 'queryId' ;

    /**
     * Returns the internal query identifier.
     *
     * @return string The current value of the query ID.
     *
     * @example
     * ```php
     * $id = $this->getQueryID();
     * ```
     */
    public function getQueryID() :string
    {
        return $this->queryId ;
    }

    /**
     * Sets the internal query identifier.
     *
     * Accepts a string value or an associative array that contains the key 'queryId'.
     * If null is passed, a default ID is auto-generated using `query_<random>`.
     *
     * @param string|array|null $init The initial ID value, or an array with key 'queryId'.
     * @return void
     *
     * @example
     * ```php
     * $this->setQueryID('my_query'); // sets queryId to 'my_query'
     *
     * $this->setQueryID(['queryId' => 'users_by_name']);
     *
     * $this->setQueryID(null); // auto-generates ID like 'query_2384721'
     * ```
     */
    public function setQueryID( string|array|null $init ) :void
    {
        if( is_array( $init ) )
        {
            $init = $init[ static::QUERY_ID ] ?? null ;
        }

        $this->queryId = is_null( $init )
                       ? static::QUERY . Char::UNDERLINE . mt_rand( 100000 , 999999 )
                       : (string) $init ;
    }
}