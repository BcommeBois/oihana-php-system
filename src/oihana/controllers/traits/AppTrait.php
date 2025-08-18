<?php

namespace oihana\controllers\traits ;

use RuntimeException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use Slim\App;

use oihana\controllers\enums\ControllerParam;

use function oihana\core\strings\formatRequestArgs;
use function oihana\files\path\joinPaths;

/**
 * Trait providing helpers to manage the application instance and generate URLs.
 *
 * This trait allows controllers to access the Slim App instance, retrieve
 * the application's base path, and build URLs with optional query parameters.
 *
 * Note: You can define a `baseUrl` in the DI container to use it consistently
 * across all controllers.
 */
trait AppTrait
{
    use BaseUrlTrait ;

    /**
     * The Slim App instance.
     *
     * @var App
     * @access protected
     */
    protected App $app ;

    /**
     * Returns the base path of the application.
     *
     * This corresponds to the path configured in Slim and can be used
     * to generate URLs relative to the application root.
     *
     * @return string The application's base path (e.g., "/myapp")
     */
    public function getBasePath() :string
    {
        return $this->app->getBasePath() ;
    }

    /**
     * Generates a full URL for the application.
     *
     * The URL is constructed using the application's base URL, the base path,
     * the optional path, and query parameters.
     *
     * @param string $path   Optional relative path to append to the base URL.
     * @param array  $params Optional query parameters as key-value pairs.
     * @param bool   $useNow If true, timestamp-like parameters will be processed immediately.
     *
     * @return string The full URL including base URL, path, and query parameters.
     */
    public function getUrl( string $path = '' , array $params = [] , bool $useNow = false ) :string
    {
        return joinPaths( $this->baseUrl , $this->app->getBasePath() , $path ) . formatRequestArgs( $params , $useNow ) ;
    }

    /**
     * Initializes the internal `app` property.
     *
     * This method retrieves the Slim App instance from either the provided
     * initialization array or the DI container. It throws an exception if
     * no valid App instance can be found.
     *
     * @param array $init Optional initialization array (e.g., ['app' => App instance]).
     * @param ContainerInterface|null $container Optional DI container for retrieving the App instance.
     *
     * @return static Returns the current controller instance for method chaining.
     *
     * @throws NotFoundExceptionInterface If the container is used and the App class is not found.
     * @throws ContainerExceptionInterface If the container throws an internal error.
     * @throws RuntimeException If no valid App instance is provided or found.
     */
    public function initializeApp( array $init = [] , ?ContainerInterface $container = null  ):static
    {
        $app = $init[ ControllerParam::APP ] ?? null;

        if( $container instanceof ContainerInterface && $container->has( App::class ) )
        {
            $app = $container->get( App::class ) ;
        }

        if( !$app instanceof App )
        {
            throw new RuntimeException( 'The controller `app` property must be defined.' ) ;
        }

        $this->app = $app ;

        return $this ;
    }
}