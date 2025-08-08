<?php

namespace oihana\traits;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\enums\Char;

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
     * The base path of the file to load an external config.
     * @var string|mixed
     */
    public string $configPath = Char::EMPTY ;

    /**
     * The 'config' parameter constant.
     */
    public const string CONFIG = 'config' ;

    /**
     * The 'configPath' parameter constant.
     */
    public const string CONFIG_PATH = 'configPath' ;

    /**
     * Initialize the config definition.
     * @param array $init
     * @param ContainerInterface|null $container
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @return void
     */
    protected function initConfig( array $init = [] , ?ContainerInterface $container = null ) :void
    {
        $config = $init[ static::CONFIG ] ?? null ;

        if( is_string( $config ) && isset( $container ) && $container->has( $config ) )
        {
            $config = $container->get( $config ) ;
        }

        $this->config = is_array( $config ) ? $config : $this->config ;
    }

    /**
     * Initialize the config path.
     * @param array $init
     * @param ContainerInterface|null $container
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     * @return void
     */
    protected function initConfigPath( array $init = [] , ?ContainerInterface $container = null ) :void
    {
        $config = $init[ static::CONFIG_PATH ] ?? null ;

        if( is_string( $config ) && isset( $container ) && $container->has( $config ) )
        {
            $config = $container->get( $config ) ;
        }

        $this->configPath = $config ?? $this->config ;
    }
}