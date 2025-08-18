<?php

namespace oihana\routes;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use Psr\Log\LoggerInterface;

use Slim\App;

use oihana\enums\Char;
use oihana\routes\http\GetRoute;
use oihana\traits\ContainerTrait;
use oihana\traits\ToStringTrait;

use function oihana\core\arrays\isAssociative;

/**
 * Represents a route definition and handles route creation and execution.
 */
class Route
{
    /**
     * Initializes a route instance with optional parameters.
     *
     * @param Container $container DI container
     * @param array     $init Optional route initialization array:
     *  - 'controllerID': Optional controller identifier.
     *  - 'name': Optional route name (defaults to generated name).
     *  - 'ownerPattern': Optional owner route pattern (default '{owner:[0-9]+}').
     *  - 'prefix': Optional prefix for route name.
     *  - 'property': Optional property name in complex routes.
     *  - 'route': Optional main route pattern.
     *  - 'routePattern': Optional route regex pattern (default '{id:[0-9]+}').
     *  - 'routes': Optional nested route definitions to initialize.
     *  - 'suffix': Optional suffix for route name.
     *  - 'verbose': Optional verbose mode (default true).
     *
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct( Container $container , array $init = [] )
    {
        $this->container = $container ;
        $this->app       = $container->get( App::class ) ;
        $this->logger    = $this->container->get( LoggerInterface::class ) ;
        $this->settings  = $init ; // TODO remove it - use only to initialize the constructor

        $this->controllerID = $init[ self::CONTROLLER_ID ] ?? $this->controllerID ;
        $this->name         = $init[ self::NAME          ] ?? $this->name ;
        $this->ownerPattern = $init[ self::OWNER_PATTERN ] ?? $this->ownerPattern ;
        $this->prefix       = $init[ self::PREFIX        ] ?? $this->prefix ;
        $this->property     = $init[ self::PROPERTY      ] ?? $this->property ;
        $this->route        = $init[ self::ROUTE         ] ?? $this->route ;
        $this->routePattern = $init[ self::ROUTE_PATTERN ] ?? $this->routePattern ;
        $this->routes       = $init[ self::ROUTES        ] ?? $this->routes ;
        $this->suffix       = $init[ self::SUFFIX        ] ?? $this->suffix ;
        $this->verbose      = $init[ self::VERBOSE       ] ?? $this->verbose ;
    }

    use ContainerTrait ,
        ToStringTrait  ;

    /**
     * Default API prefix for route names
     */
    public const string DEFAULT_PREFIX = 'api' ;

    /**
     * Default route pattern for numeric IDs
     */
    public const string DEFAULT_ROUTE_PATTERN = '{id:[0-9]+}' ;

    /**
     * Default owner pattern for numeric owner IDs
     */
    public const string DEFAULT_OWNER_PATTERN = '{owner:[0-9]+}' ;

    /**
     * Array keys for route initialization
     */
    public const string CLAZZ         = 'clazz'        ;
    public const string CONTROLLER_ID = 'controllerID' ;
    public const string METHOD        = 'method'       ;
    public const string NAME          = 'name'         ;
    public const string OWNER_PATTERN = 'ownerPattern' ;
    public const string PATCH_PATTERN = 'patchPattern' ;
    public const string PREFIX        = 'prefix'       ;
    public const string PROPERTY      = 'property'     ;
    public const string ROUTE         = 'route'        ;
    public const string ROUTE_PATTERN = 'routePattern' ;
    public const string ROUTES        = 'routes'       ;
    public const string SUFFIX        = 'suffix'       ;
    public const string VERBOSE       = 'verbose'      ;

    /**
     * @var App Slim App instance
     */
    protected App $app ;

    /**
     * @var string|null Controller ID registered in DI container
     */
    public ?string $controllerID = null ;

    /**
     * @var LoggerInterface Logger instance
     */
    public LoggerInterface $logger ;

    /**
     * @var string|null Route name
     */
    public ?string $name = null ;

    /**
     * @var string|null Owner route pattern
     */
    public ?string $ownerPattern = self::DEFAULT_OWNER_PATTERN ;

    /**
     * @var string Route name prefix
     */
    public string $prefix = self::DEFAULT_PREFIX ;

    /**
     * @var string Property name in complex routes
     */
    public string $property = Char::EMPTY ;

    /**
     * @var string|null Main route pattern
     */
    public ?string $route = null ;

    /**
     * @var string Main route pattern regex
     */
    public  string $routePattern = self::DEFAULT_ROUTE_PATTERN ;

    /**
     * @var array|null Nested route definitions
     */
    public ?array $routes = null ;

    /**
     * @var array Initial settings passed to constructor
     */
    public array $settings ;

    /**
     * @var string Route name suffix
     */
    public string $suffix = Char::EMPTY ;

    /**
     * @var bool Verbose mode flag
     */
    public bool $verbose = true ;


    /**
     * Invokes all nested routes if defined.
     *
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __invoke(): void
    {
        if( is_array( $this->routes ) && count( $this->routes ) > 0 )
        {
            foreach( $this->routes as $definition )
            {
                $route = $this->create( $definition ) ;
                if( $route instanceof Route )
                {
                    $route() ;
                }
            }
        }
    }

    /**
     * Removes null or empty string values from an array of parameters.
     *
     * @param array $params Parameters to clean
     * @return array Filtered parameters
     */
    public function cleanParams( array $params = [] ) :array
    {
        return array_filter
        (
            $params ,
            fn( $value ) => ( !is_null( $value ) && $value !== Char::EMPTY )
        ) ;
    }

    /**
     * Creates a new Route instance from a definition array or Route object.
     *
     * @param array|Route|null $definition Route definition or existing Route
     * @return Route|null The created Route instance or null if invalid
     */
    public function create( array|Route|null $definition ) :?Route
    {
        if( $definition instanceof Route )
        {
            return $definition ;
        }

        if( is_array( $definition ) && isAssociative( $definition ) )
        {
            $clazz = $definition[ self::CLAZZ ] ?? GetRoute::class ;
            $route = new $clazz( $this->container , $this->cleanParams
            ([
                Route::CONTROLLER_ID => $definition[ Route::CONTROLLER_ID ] ?? $this->controllerID ?? null ,
                Route::METHOD        => $definition[ Route::METHOD        ] ?? null ,
                Route::NAME          => $definition[ Route::NAME          ] ?? null ,
                Route::PROPERTY      => $definition[ Route::PROPERTY      ] ?? null ,
                Route::ROUTE         => $definition[ Route::ROUTE         ] ?? $this->route ,
                Route::SUFFIX        => $definition[ Route::SUFFIX        ] ?? null ,
            ])) ;

            if( $route instanceof Route )
            {
                return $route;
            }
        }

        return null ;
    }

    /**
     * Converts a route path from 'foo/bar' to 'foo.bar'.
     *
     * @param string $route Route path
     * @return string Dotified route path
     */
    public function dotify( string $route ) :string
    {
        if( str_contains( $route , Char::SLASH  ) )
        {
            return str_replace( Char::SLASH  , Char::DOT , $route ) ; // Transform the 'foo/bar' route path in 'foo.bar'
        }
        return $route ;
    }

    /**
     * Executes a callable or an array of callables.
     *
     * @param mixed $routes Callable or array of callables
     * @return void
     */
    public function execute( mixed $routes ) :void
    {
        if( is_callable( $routes ) )
        {
            $routes() ;
        }
        else if( is_array( $routes ) )
        {
            foreach ( $routes as $route )
            {
                if( is_callable( $route ) )
                {
                    $route() ;
                }
            }
        }
    }

    /**
     * Returns the fully qualified route name, including prefix and suffix.
     *
     * @return string Route name
     */
    public function getName() :string
    {
        $name = trim( $this->name ?? $this->dotify( $this->getRoute() )  , Char::DOT ) ;
        return trim( implode( Char::DOT , [ $this->prefix , $name , $this->suffix ] ) , Char::DOT ) ;
    }

    /**
     * Returns the safe main route representation starting with '/'.
     *
     * @return string Route path
     */
    public function getRoute() :string
    {
        return Char::SLASH . ltrim( $this->route ?? Char::EMPTY , Char::SLASH ) ;
    }
}