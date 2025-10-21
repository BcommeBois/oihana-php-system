<?php

namespace oihana\controllers ;

use ReflectionException;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

use oihana\controllers\traits\ApiTrait;
use oihana\controllers\traits\AppTrait;
use oihana\controllers\traits\BenchTrait;
use oihana\controllers\traits\GetParamTrait;
use oihana\controllers\traits\HttpCacheTrait;
use oihana\controllers\traits\JsonTrait;
use oihana\controllers\traits\MockTrait;
use oihana\controllers\traits\PaginationTrait;
use oihana\controllers\traits\PathTrait;
use oihana\controllers\traits\RouterTrait;
use oihana\controllers\traits\StatusTrait;
use oihana\controllers\traits\ValidatorTrait;
use oihana\enums\Char;
use oihana\enums\http\HttpHeader;
use oihana\logging\LoggerTrait;
use oihana\traits\ConfigTrait;
use oihana\traits\ContainerTrait;
use oihana\traits\ToStringTrait;

/**
 * Base abstract Controller.
 *
 * Provides the foundational logic for all controllers, including:
 * dependency injection, logging, routing, validation, caching,
 * JSON handling, benchmarking, configuration, and API initialization.
 *
 * This class is meant to be extended by all application controllers.
 *
 * @package oihana\controllers
 */
abstract class Controller
{
    /**
     * Creates a new Controller instance.
     *
     * @param Container $container The DI container reference to initialize the controller.
     * @param array $init The optional properties to passed-in to initialize the object.
     * - string $path : The path expression of the component.
     * - Validator $validator : The optional validator of the controller (by default use a basic Validator instance).
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DependencyException
     * @throws NotFoundException
     * @throws ReflectionException
     */
    public function __construct( Container $container , array $init = [] )
    {
        $this->container = $container ;
        $this->initializeApi            ( $init , $container )
             ->initializeApp            ( $init , $container )
             ->initializeBaseUrl        ( $init , $container )
             ->initializeConfig         ( $init , $container )
             ->initializeHttpCache      ( $init , $container )
             ->initializeJsonOptions    ( $init , $container )
             ->initializeLoggable       ( $init , $container )
             ->initializeLogger         ( $init , $container )
             ->initializePagination     ( $init , $container )
             ->initializeRouterParser   ( $init , $container )
             ->initializeBench          ( $init )
             ->initializeMock           ( $init )
             ->initializePath           ( $init )
             ->initializeParamsStrategy ( $init )
             ->initializeValidator      ( $init ) ; // https://github.com/somnambulist-tech/validation
    }

    use ApiTrait        ,
        AppTrait        ,
        BenchTrait      ,
        ContainerTrait  ,
        ConfigTrait     ,
        GetParamTrait   ,
        HttpCacheTrait  ,
        JsonTrait       ,
        LoggerTrait     ,
        MockTrait       ,
        PaginationTrait ,
        PathTrait       ,
        RouterTrait     ,
        StatusTrait     ,
        ToStringTrait   ,
        ValidatorTrait  ;

    /**
     * The optional conditions settings to validate things.
     */
    public ?array $conditions ;

    /**
     * The full path reference.
     */
    public string $fullPath ;

    /**
     * The path reference.
     */
    public string $path = Char::EMPTY ;

    /**
     * The path of an owner reference.
     * @var string|null
     */
    public ?string $ownerPath = Char::EMPTY ;

    /**
     * Alter the specific thing.
     * Overrides this method to customize the alter strategy.
     * @param mixed $thing A generic object to alter.
     * @param ?string $lang The lang optional lang iso code.
     * @param ?string $skin The optional skin mode.
     * @param ?array $params The optional params object.
     * @return mixed The altered thing reference.
     */
    public function alter( mixed $thing = null , ?string $lang = null , ?string $skin = null , ?array $params = null ) :mixed
    {
        return $thing ;
    }

    /**
     * Returns the current Request route reference.
     * @param ?Request $request
     * @return RouteInterface|null
     */
    public function getRoute( ?Request $request ) :?RouteInterface
    {
        return isset( $request ) ? RouteContext::fromRequest( $request )->getRoute() : null ;
    }

    /**
     * Redirect to a specific URL target.
     * @param Response $response
     * @param string $url
     * @param int $status
     * @return Response
     */
    public function redirectResponse( Response $response , string $url , int $status = 302 ) :Response
    {
        return $response->withHeader( HttpHeader::LOCATION , $url )->withStatus( $status ) ;
    }
}