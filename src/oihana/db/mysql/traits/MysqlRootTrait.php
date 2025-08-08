<?php

namespace oihana\db\mysql\traits;

use DI\DependencyException;
use DI\NotFoundException;

use oihana\db\mysql\MysqlModel;
use oihana\models\pdo\PDOTrait;
use oihana\traits\ContainerTrait;

/**
 * Provides support for accessing and initializing a root-level MySQL administrative model,
 * typically used to perform high-privilege operations via a separate `MysqlModel` instance.
 *
 * This trait depends on a PSR-11 compatible container and expects a `MysqlModel` instance
 * to be provided directly or by reference (as a container service name).
 *
 * @package oihana\db\mysql\traits
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
trait MysqlRootTrait
{
    use ContainerTrait ,
        MysqlAssertionsTrait ,
        PDOTrait ;

    /**
     * The 'mysqlRoot' parameter value.
     */
    public const string MYSQL_ROOT = 'mysqlRoot' ;

    /**
     * The mysql root model reference.
     * @var ?MysqlModel
     */
    public ?MysqlModel $mysqlRoot = null ;

    /**
     * Initialize the Mysql model reference.
     * @param array $init
     * @return static
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function initializeMysqlRoot( array $init = [] ) :static
    {
        $mysqlRoot = $init[ static::MYSQL_ROOT ] ?? $this->mysqlRoot ;
        if( is_string( $mysqlRoot ) && $this->container->has( $mysqlRoot ) )
        {
            $mysqlRoot = $this->container->get( $mysqlRoot ) ;
        }
        $this->mysqlRoot = $mysqlRoot instanceof MysqlModel ? $mysqlRoot : null ;
        return $this ;
    }
}