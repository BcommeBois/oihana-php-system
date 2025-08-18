<?php

namespace oihana\controllers\traits\prepare;

use DateTimeZone;
use Exception;

use oihana\controllers\enums\ControllerParam;
use Psr\Http\Message\ServerRequestInterface as Request;

trait PrepareTimezone
{
    /**
     * Prepare the timezone component.
     * @throws Exception
     */
    protected function prepareTimezone
    (
        ?Request $request ,
        ?array   &$params ,
        ?string  &$timezone ,
        ?array   $timeOptions ,
        string   $defaultValue = 'Europe/Paris'
    )
    :void
    {
        if( isset( $request ) )
        {
            $params[ ControllerParam::TIMEZONE ]
            = $timezone
            = new DateTimeZone( $this->getParam( $request , ControllerParam::TIMEZONE ) ?? $timeOptions[ ControllerParam::TIMEZONE_DEFAULT ] ?? $defaultValue ) ;
        }
    }
}