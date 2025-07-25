<?php

namespace oihana\traits;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Provides functionality for managing a configuration setup.
 */
trait ConfigTrait
{
    /**
     * The config reference.
     */
    public array $config = [] ;

    /**
     * The 'config' parameter constant.
     */
    public const string CONFIG = 'config' ;

    /**
     * Initialize the configuration definition.
     * @param array $init
     * @param ContainerInterface|null $container
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @return array
     */
    protected function initConfig( array $init = [] , ?ContainerInterface $container = null ) :array
    {
        $config = $init[ static::CONFIG ] ?? null ;

        if( is_string( $config ) && isset( $container ) && $container->has( $config ) )
        {
            $config = $container->get( $config ) ;
        }

        return is_array( $config ) ? $config : $this->config ;
    }
}