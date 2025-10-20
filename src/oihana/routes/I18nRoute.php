<?php

namespace oihana\routes;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

use oihana\enums\Char;
use oihana\enums\http\HttpMethod;
use oihana\routes\traits\HttpMethodRoutesTrait;

use function oihana\routes\helpers\withPlaceholder;

class I18nRoute extends Route
{
    public function __construct( Container $container , array $init = [] )
    {
        parent::__construct( $container , $init ) ;
        $this->initializeFlags( $init ) ;
        $this->initializeMethods( $init ) ;
    }

    use HttpMethodRoutesTrait ;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __invoke(): void
    {
        if ( $this->container->has( $this->controllerID ) )
        {
            $routes = [] ;

            // route/{id}/property
            $route = withPlaceholder( $this->getRoute() , $this->routePlaceholder ) . Char::SLASH . $this->property ;

            $this->options ( $routes , $route ) ;
            $this->get     ( $routes , $route , $this->property ) ;
            $this->patch   ( $routes , $route , HttpMethod::patch . ucfirst( $this->property ) ) ;

            if( count( $routes ) > 0 )
            {
                $this->execute( $routes );
            }
        }
        else
        {
            $this->logger->warning( $this . ' invoke failed, the controller \'' . $this->controllerID . '\' is not registered in the DI container.');
        }
    }

}