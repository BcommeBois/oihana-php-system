<?php

namespace oihana\controllers\traits\prepare;

use oihana\controllers\enums\ControllerParam;
use Psr\Http\Message\ServerRequestInterface as Request;

trait PrepareActive
{
    protected function prepareActive( ?Request $request , array $args = [] , bool $defaultValue = true ) :bool|null
    {
        $active = $args[ ControllerParam::ACTIVE ] ?? $defaultValue ;
        if( isset( $request ) )
        {
            $param = $this->getQueryParam( $request , ControllerParam::ACTIVE ) ; // query param only (not body)
            if( $param == '0' || $param == 'false' || $param == 'FALSE' )
            {
                $active = false ;
            }
        }
        return $active ;
    }
}