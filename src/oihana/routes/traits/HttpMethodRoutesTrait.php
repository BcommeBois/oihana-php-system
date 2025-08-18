<?php

namespace oihana\routes\traits;

use DI\DependencyException;
use DI\NotFoundException;
use oihana\enums\Char;
use oihana\enums\http\HttpMethod;
use oihana\routes\http\DeleteRoute;
use oihana\routes\http\GetRoute;
use oihana\routes\http\OptionsRoute;
use oihana\routes\http\PatchRoute;
use oihana\routes\http\PostRoute;
use oihana\routes\http\PutRoute;
use oihana\routes\Route;

trait HttpMethodRoutesTrait
{
    use HasRouteTrait ;

    public ?string $delete = null ;
    public ?string $get    = null ;
    public ?string $patch  = null ;
    public ?string $post   = null ;
    public ?string $put    = null ;

    /**
     * Initialize the internal flags.
     * @param array $init
     * @return void
     */
    protected function initializeMethods( array $init = [] ) :void
    {
        $this->delete = $init[ HttpMethod::delete ] ?? $this->delete ;
        $this->get    = $init[ HttpMethod::get    ] ?? $this->get    ;
        $this->patch  = $init[ HttpMethod::patch  ] ?? $this->patch  ;
        $this->post   = $init[ HttpMethod::post   ] ?? $this->post   ;
        $this->put    = $init[ HttpMethod::put    ] ?? $this->put    ;
    }

    /**
     * Generates a special GET count route reference.
     */
    protected function count( array &$routes , string $route , ?string $method = HttpMethod::count ) :void
    {
        if( $this->hasCount )
        {
            $this->method( GetRoute::class , $routes , $route . Char::SLASH . HttpMethod::count , $method ) ;
        }
    }

    /**
     * Generates a new DELETE route reference.
     */
    protected function delete( array &$routes , string $route , ?string $method = null ) :void
    {
        if( $this->hasDelete )
        {
            $this->method( DeleteRoute::class , $routes , $route , $method ?? $this->delete ) ;
        }
    }

    /**
     * Generates a new DELETE (ALL) route reference.
     */
    protected function deleteAll( array &$routes , string $route , ?string $method = HttpMethod::deleteAll ) :void
    {
        if( $this->hasDeleteAll )
        {
            $this->method( DeleteRoute::class , $routes , $route , $method ) ;
        }
    }

    /**
     * Generates a new GET route reference.
     */
    protected function get( array &$routes , string $route , ?string $method = null ) :void
    {
        if( $this->hasGet )
        {
            $this->method( GetRoute::class , $routes , $route , $method ?? $this->get ) ;
        }
    }

    /**
     * Generates a new GET (LIST) route reference.
     */
    protected function list( array &$routes , string $route , ?string $method = HttpMethod::list ) :void
    {
        if( $this->hasList )
        {
            $this->method( GetRoute::class , $routes , $route , $method ) ;
        }
    }

    /**
     * @protected
     * @param string $clazz
     * @param array $routes
     * @param string $route
     * @param ?string $method
     * @return void
     */
    protected function method( string $clazz , array &$routes , string $route , ?string $method = null ) :void
    {
        $routes[] = new $clazz( $this->container , $this->cleanParams
        ([
            Route::CONTROLLER_ID => $this->controllerID ,
            Route::METHOD        => $method ,
            Route::ROUTE         => $route
        ]) ) ;
    }

    /**
     * Generates a new OPTIONS route reference.
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function options( array &$routes , string $route , bool $flag = true ) :void
    {
        if ( $flag )
        {
            $routes[] = new OptionsRoute
            (
                $this->container ,
                [
                    Route::CONTROLLER_ID => $this->controllerID ,
                    Route::ROUTE         => $route
                ]
            ) ;
        }
    }

    /**
     * Generates a new PATCH route reference.
     */
    protected function patch( array &$routes , string $route , ?string $method = null ) :void
    {
        if( $this->hasPatch )
        {
            $this->method( PatchRoute::class , $routes , $route , $method ?? $this->patch ) ;
        }
    }

    /**
     * Generates a new POST route reference.
     */
    protected function post( array &$routes , string $route , ?string $method = null ) :void
    {
        if( $this->hasPost )
        {
            $this->method( PostRoute::class , $routes , $route , $method ?? $this->post ) ;
        }
    }

    /**
     * Generates a new PUT route reference.
     */
    protected function put( array &$routes , string $route , ?string $method = null ) :void
    {
        if( $this->hasPut )
        {
            $this->method( PutRoute::class , $routes , $route , $method ?? $this->put ) ;
        }
    }
}