<?php

namespace oihana\routes\http;

use oihana\routes\Route;

use function oihana\routes\helpers\responsePassthrough;

class OptionsRoute extends Route
{
    /**
     * Called when a script tries to call the instance as a function.
     */
    public function __invoke(): void
    {
        $this->app->options( $this->getRoute() , responsePassthrough(...) ) ;
    }
}