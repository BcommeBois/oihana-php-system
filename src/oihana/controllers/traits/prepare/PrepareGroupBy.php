<?php

namespace oihana\controllers\traits\prepare;

use DI\NotFoundException;
use oihana\controllers\enums\ControllerParam;
use Psr\Http\Message\ServerRequestInterface as Request;

trait PrepareGroupBy
{
    /**
     * @throws NotFoundException
     */
    protected function prepareGroupBy( ?Request $request , ?array &$params , ?string &$groupBy ) :void
    {
        if( isset( $request ) )
        {
            $value = $this->getParam( $request , ControllerParam::GROUP_BY );
            if( isset( $value ) )
            {
                $groupBy = $value ;
                if( !empty( $groupBy ) && $params )
                {
                    $params[ ControllerParam::GROUP_BY ] = $groupBy ;
                }
            }
        }
    }
}