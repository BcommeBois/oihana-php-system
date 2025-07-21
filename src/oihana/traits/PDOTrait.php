<?php

namespace oihana\traits;

use Exception;
use PDO;
use PDOStatement;

use DI\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\enums\Char;
use oihana\enums\Param;;

/**
 * Provides methods for binding values, executing queries, and retrieving results using PDO.
 * Supports schema-based result mapping and integration with dependency injection.
 *
 * Requires the following traits:
 * - AlterDocumentTrait
 * - BindsTrait
 * - DebugTrait
 * - ToStringTrait
 *
 * @package oihana\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait PDOTrait
{
    use AlterDocumentTrait ,
        BindsTrait ,
        DebugTrait ,
        ToStringTrait ;

    /**
     * Indicates if the the constructor is called before setting properties.
     * Only if the schema property is defined.
     * @var bool|null
     */
    public ?bool $deferAssignment = false ;

    /**
     * The PDO reference.
     * @var ?PDO
     */
    public ?PDO $pdo = null ;

    /**
     * The internal schema to use in the PDO fetch processes.
     * @var string|mixed|null
     */
    public ?string $schema = null ;

    /**
     * Bind named parameters to a prepared PDO statement.
     *
     * @param PDOStatement $statement  The PDO statement.
     * @param array        $bindVars   Associative array of bindings. Supports:
     *                                 - ['id' => 5]
     *                                 - ['id' => [5, PDO::PARAM_INT]]
     * @return void
     */
    public function bindValues( PDOStatement $statement , array $bindVars = [] ):void
    {
        // $this->logger?->info( __METHOD__ . ' bindVars : ' . json_encode( $bindVars ) ) ;
        if( is_array( $bindVars ) && count( $bindVars ) > 0  )
        {
            foreach ( $bindVars as $key => $value )
            {
                if( is_array( $value ) )
                {
                    [ $typedValue , $type ] = $value ;
                    $statement->bindValue( Char::COLON . $key , $typedValue , $type );
                }
                else
                {
                    $statement->bindValue( Char::COLON . $key , $value );
                }
            }
        }
    }

    /**
     * Execute a SELECT query and fetch a single result.
     * The result is returned as an object or as a mapped schema class if defined.
     * Alteration is applied via AlterDocumentTrait.
     *
     * @param string $query     The SQL query to execute.
     * @param array  $bindVars  Optional bindings for the query.
     *
     * @return mixed|null       The result object or null if not found.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fetch( string $query , array $bindVars = [] ): mixed
    {
        $statement = $this->pdo?->prepare( $query ) ;
        if( $statement instanceof PDOStatement )
        {
            try
            {
                $this->bindValues( $statement , $bindVars ) ;
                if( $statement->execute() )
                {
                    $this->initializeDefaultFetchMode( $statement ) ;
                    $row = $statement->fetch() ;
                    $result = $row === false ? null : (object) $row ;
                    $statement->closeCursor() ;
                    $statement = null ;
                    return $this->alter( $result ) ;
                }
            }
            catch ( Exception $exception )
            {
                $this->logger->warning( __METHOD__ . ' failed, ' . $exception->getMessage() ) ;
            }
        }
        $statement = null ;
        return null ;
    }

    /**
     * Execute a SELECT query and fetch all results.
     * Results are returned as an array of associative arrays or schema instances.
     * Alteration is applied via AlterDocumentTrait.
     *
     * @param string $query     The SQL query to execute.
     * @param array  $bindVars  Optional bindings for the query.
     *
     * @return array            An array of results.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function fetchAll( string $query , array $bindVars = [] ) :array
    {
        $result    = [] ;
        $statement = $this->pdo?->prepare( $query ) ;
        if( $statement instanceof PDOStatement )
        {
            try
            {
                $this->bindValues( $statement , $bindVars ) ;
                if( $statement->execute() )
                {
                    $this->initializeDefaultFetchMode( $statement ) ;
                    $result = $statement->fetchAll() ;
                    $statement->closeCursor() ;
                    if( count( $result ) > 0 )
                    {
                        $result = $this->alter( $result ) ;
                    }
                }
            }
            catch ( Exception $exception )
            {
                $this->logger->warning( __METHOD__ . ' failed, ' . $exception->getMessage() ) ;
            }
        }
        $statement = null ;
        return $result ;
    }

    /**
     * Execute a query and return the value of a single column from the first row.
     *
     * @param string $query     The SQL query to execute.
     * @param array  $bindVars  Optional bindings for the query.
     * @param int    $column    Column index (0-based) to return from the first row.
     *
     * @return mixed            The column value or 0 if the query fails.
     */
    public function fetchColumn( string $query , array $bindVars = [] , int $column = 0 ) :mixed
    {
        $statement = $this->pdo?->prepare( $query ) ;
        if( $statement instanceof PDOStatement )
        {
            try
            {
                $this->bindValues( $statement , $bindVars ) ;
                if( $statement->execute() )
                {
                    $result = $statement->fetchColumn( $column ) ;
                    $statement->closeCursor() ;
                    $statement = null ;
                    return $result ;
                }
            }
            catch ( Exception $exception )
            {
                $this->logger->warning( __METHOD__ . ' failed, ' . $exception->getMessage() ) ;
            }
        }
        $statement = null ;
        return 0 ;
    }

    /**
     * Initialize the PDO instance from a config array or dependency injection container.
     *
     * @param array         $init      Configuration array. Expects Param::PDO as key.
     * @param Container|null $container Optional DI container to resolve the PDO service.
     *
     * @return PDO|null                 The resolved PDO instance or null.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function initPDO( array $init = [] , ?Container $container = null ) :?PDO
    {
        $pdo = $init[ Param::PDO ] ?? null  ;
        if( isset( $container ) && is_string( $pdo ) && $container->has( $pdo ) )
        {
            $pdo = $container->get( $pdo ) ;
        }
        return $pdo instanceof PDO ? $pdo : null ;
    }

    /**
     * Set the default fetch mode on the statement.
     * Uses FETCH_ASSOC by default or FETCH_CLASS (with optional FETCH_PROPS_LATE)
     * if a schema class is defined and exists.
     *
     * @param PDOStatement $statement  The PDO statement to configure.
     *
     * @return void
     */
    public function initializeDefaultFetchMode( PDOStatement $statement ):void
    {
        if( isset( $this->schema ) && class_exists( $this->schema ) )
        {
            $mode = PDO::FETCH_CLASS ;
            if( $this->deferAssignment )
            {
                $mode |= PDO::FETCH_PROPS_LATE ;
            }
            $statement->setFetchMode( $mode , $this->schema ) ;
        }
        else
        {
            $statement->setFetchMode( PDO::FETCH_ASSOC ) ;
        }
    }
}