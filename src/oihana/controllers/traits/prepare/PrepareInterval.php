<?php

namespace oihana\controllers\traits\prepare;

use DI\NotFoundException;

use oihana\controllers\enums\ControllerParam;
use oihana\enums\FilterOption;

use Psr\Http\Message\ServerRequestInterface as Request;

trait PrepareInterval
{
    /**
     * @throws NotFoundException
     */
    protected function prepareInterval(?Request $request , array &$params , ?string &$interval , ?array $timeOptions ) :void
    {
        $register = false ;
        if( isset( $request ) )
        {
            $value = $this->getParam( $request , ControllerParam::INTERVAL ) ;
            if( isset( $value ) )
            {
                $register = true ;
                $interval = filter_var( $value , FILTER_VALIDATE_INT ,
                [
                    FilterOption::OPTIONS =>
                    [
                        FilterOption::MIN_RANGE => 1,
                        FilterOption::MAX_RANGE => intval( $timeOptions[ FilterOption::MAX_RANGE ] )
                    ]
                ]) ;
            }
        }

        if( is_null($interval) || $interval === false )
        {
            $interval = (int) $timeOptions[ ControllerParam::INTERVAL_DEFAULT ] ;
        }

        if( $register )
        {
            $params[ ControllerParam::INTERVAL ] = $interval ;
        }
    }
}