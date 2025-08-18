<?php

namespace oihana\routes\http;

use oihana\routes\Route;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OptionsRoute extends Route
{
    /**
     * Called when a script tries to call the instance as a function.
     */
    public function __invoke(): void
    {
        $this->app->options( $this->getRoute() , fn( Request $request , Response $response ) => $response ); ;
    }
}