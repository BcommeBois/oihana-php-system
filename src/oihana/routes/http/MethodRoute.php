<?php

namespace oihana\routes\http;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use oihana\enums\http\HttpMethod;
use oihana\routes\Route;

class MethodRoute extends Route
{
    /**
     * Creates a new MethodRoute instance.
     * @param Container $container The DI Container reference.
     * @param array $init The optional settings object.
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct( Container $container , array $init = [] )
    {
        parent::__construct( $container , $init );
        $this->method = $init[ static::METHOD ] ?? static::INTERNAL_METHOD ;
    }

    /**
     * The name of the method to call in the controller to invoke with this route.
     * @var string|mixed
     */
    public string $method ;

    /**
     * The default internal method name of the controller to invoke with this route.
     */
    const string INTERNAL_METHOD = HttpMethod::get ;

    /**
     * Called when a script tries to call the instance as a function.
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __invoke(): void
    {
        if( $this->container->has( $this->controllerID ) )
        {
            $controller = $this->container->get( $this->controllerID ) ;
            if( isset( $controller ) && method_exists( $controller , $this->method ) )
            {
                // if( $this->verbose )
                // {
                //     $this->logger->info( $this . ' invoke name: ' . $this->getName() );
                // }
                $this->app->{ static::INTERNAL_METHOD }( $this->getRoute() , [ $controller , $this->method ] )->setName( $this->getName() ) ;
            }
            else
            {
                $this->logger->warning( $this . ' invoke failed, the method "' . $this->method . '" is not defined in the controller "' . $this->controllerID . '".' );
            }
        }
        else
        {
            $this->logger->warning( $this . ' invoke failed, the controller "' . $this->controllerID . '" is not registered in the DI container.' );
        }
    }
}