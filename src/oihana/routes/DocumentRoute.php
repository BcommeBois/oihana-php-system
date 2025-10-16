<?php

namespace oihana\routes;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use oihana\enums\Char;
use oihana\routes\traits\HttpMethodRoutesTrait;
use function oihana\core\strings\betweenBrackets;
use function oihana\routes\helpers\withPlaceholder;

class DocumentRoute extends Route
{
    public function __construct( Container $container , array $init = [] )
    {
        parent::__construct( $container , $init ) ;
        $this->initializeFlags( $init ) ;
        $this->initializeMethods( $init ) ;
    }

    use HttpMethodRoutesTrait ;

    /**
     * Initialize the current route.
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __invoke(): void
    {
        if ( $this->container->has( $this->controllerID ) )
        {
            $routes = [] ;

            $route = $this->getRoute() ; // "/route"

            $this->list  ( $routes , $route ) ;
            $this->count ( $routes , $route ) ;

            $this->options   ( $routes , $route , $this->hasDeleteAll || $this->hasPost ) ;
            $this->deleteAll ( $routes , $route ) ;
            $this->post      ( $routes , $route ) ;

            $thing = withPlaceholder( $route , $this->routePlaceholder ) ; // default /route/{id:[0-9]+}

            $this->options( $routes , $thing , $this->hasGet || $this->hasDelete || $this->hasPatch || $this->hasPut ) ;
            $this->delete ( $routes , $thing ) ;
            $this->get    ( $routes , $thing ) ;
            $this->patch  ( $routes , $thing ) ;
            $this->put    ( $routes , $thing ) ;

            if( count( $routes ) > 0 )
            {
                $this->execute( $routes ) ;
            }
        }
        else
        {
            $this->logger->warning( $this . ' invoke failed, the controller \'' . $this->controllerID . '\' is not registered in the DI container.' );
        }
    }
}