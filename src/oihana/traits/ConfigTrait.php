<?php

namespace oihana\traits;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\enums\Char;

/**
 * Trait ConfigTrait
 *
 * Provides functionality for managing a configuration setup.
 *
 * @property array  $config     The configuration array.
 * @property string $configPath The base path to load an external configuration file.
 *
 * Example usage:
 * ```php
 * class MyService
 * {
 *     use ConfigTrait;
 * }
 *
 * $service = new MyService();
 *
 * // Initialize directly from an array
 * $service->initConfig
 * ([
 *     'config' =>
 *     [
 *         'db_host' => 'localhost',
 *         'db_name' => 'test'
 *     ],
 *     'configPath' => '/etc/myservice/config.php'
 * ]);
 *
 * echo $service->config['db_name']; // 'test'
 * echo $service->configPath;        // '/etc/myservice/config.php'
 *
 * // Initialize using a DI container
 * $container = new DI\Container() ;
 * $container->set('my_config',
 * [
 *     'db_host' => '127.0.0.1',
 *     'db_name' => 'prod'
 * ]);
 *
 * $service->initConfig( ['config' => 'my_config'] , $container ) ;
 * echo
 */
trait ConfigTrait
{
    /**
     * @var array The config reference.
     */
    public array $config = [] ;

    /**
     * @var string The base path of the file to load an external config.
     */
    public string $configPath = Char::EMPTY ;

    /**
     * The 'config' key for initialization arrays.
     */
    public const string CONFIG = 'config' ;

    /**
     * The 'configPath' key for initialization arrays.
     */
    public const string CONFIG_PATH = 'configPath' ;

    /**
     * Initialize the configuration from an array or a DI container.
     *
     * Example usage:
     * ```php
     * $this->initConfig( ['config' => ['db_host' => 'localhost']] ) ;
     * echo $this->config['db_host'] ; // 'localhost'
     *
     * // Using a DI container
     * $container->set( 'my_config' , ['db_name' => 'prod'] ) ;
     * $this->initializeConfig( ['config' => 'my_config' ] , $container ) ;
     * echo $this->config[ 'db_name' ] ; // 'prod'
     * ```
     *
     * @param array                   $init      Initialization array that may contain 'config' key.
     * @param ContainerInterface|null $container Optional DI container to resolve config entries.
     *
     * @return static Returns the current instance for method chaining.
     *@throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     *
     * @throws ContainerExceptionInterface
     */
    public function initializeConfig( array $init = [] , ?ContainerInterface $container = null ) :static
    {
        $config = $init[ static::CONFIG ] ?? null ;
        if( is_array( $config ) )
        {
            $this->config = $config ;
        }
        else if( $container instanceof ContainerInterface )
        {
            $entry = null ;

            if( is_string( $config ) && $container->has( $config )  )
            {
                $entry = $container->get( $config ) ;
            }
            else if ( $container->has( static::CONFIG ) )
            {
                $entry = $container->get( static::CONFIG  ) ;
            }

            if ( is_array( $entry ) )
            {
                $this->config = $entry ;
            }
        }
        return $this ;
    }

    /**
     * Initialize the configuration path from an array or a DI container.
     *
     * Example usage:
     * ```php
     * $this->initConfigPath(['configPath' => '/etc/myservice/config']);
     * echo $this->configPath; // '/etc/myservice/config'
     *
     * // Using a DI container
     * $container->set('configPath', '/etc/prod/config');
     * $this->initConfigPath([], $container);
     * echo $this->configPath; // '/etc/prod/config'
     * ```
     *
     * @param array $init      Initialization array that may contain 'configPath' key.
     * @param ContainerInterface|null $container Optional DI container to resolve path entries.
     *
     * @return static Returns the current instance for method chaining.
     * @throws DependencyException
     * @throws NotFoundException
     * @throws NotFoundExceptionInterface
     *
     * @throws ContainerExceptionInterface
     */
    public function initConfigPath( array $init = [] , ?ContainerInterface $container = null ) :static
    {
        $path = $init[ static::CONFIG_PATH ] ?? null ;

        if( $container instanceof ContainerInterface )
        {
            if( is_string( $path ) && $container->has( $path ) )
            {
                $path = $container->get( $path ) ;
            }
            else if ( $container->has( static::CONFIG_PATH ) )
            {
                $path = $container->get( static::CONFIG_PATH ) ;
            }
        }

        $this->configPath = is_string( $path ) ? $path : Char::EMPTY ;

        return $this ;
    }
}