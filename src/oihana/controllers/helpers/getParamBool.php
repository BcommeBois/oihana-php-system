<?php

namespace oihana\controllers\helpers ;

use DI\NotFoundException;
use oihana\enums\Boolean;
use oihana\enums\http\HttpParamStrategy;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Retrieves a parameter from the HTTP request and ensures it is a boolean.
 *
 * This helper calls {@see getParam()} and interprets the value according to
 * {@see Boolean::TRUE} and {@see Boolean::FALSE}:
 * - If the value matches `Boolean::TRUE` or `Boolean::FALSE`, it is converted to a PHP boolean.
 * - Otherwise, the `$defaultValue` is returned.
 * - If `$throwable` is true, a {@see NotFoundException} may be thrown by `getParam()`.
 *
 * @param Request|null $request      The PSR-7 server request instance.
 * @param string       $name         The parameter name or dot-notated path.
 * @param array        $args         Optional default values passed to `getParam()`.
 * @param bool|null    $defaultValue Value returned if the parameter is missing or not a boolean. Default is null.
 * @param string       $strategy     Which source to search: `HttpParamStrategy::BOTH|QUERY|BODY`. Default is BOTH.
 * @param bool         $throwable    Whether to throw a `NotFoundException` if parameter is missing. Default false.
 *
 * @return bool|null The parameter value if it is a boolean, otherwise `$defaultValue` or null.
 *
 * @throws NotFoundException If `$throwable` is true and the parameter is not found.
 *
 * @example
 * ```php
 * // Query string: ?active=true
 * $active = getParamBool($request, 'active', [], false);
 *
 * // Body: ['enabled' => Boolean::FALSE]
 * $enabled = getParamBool($request, 'enabled', [], true);
 * ```
 *
 * @package oihana\controllers\helpers
 * @author  Marc Alcaraz (ekameleon)
 * @since   1.0.0
 */
function getParamBool
(
    ?Request $request ,
    string   $name    ,
    array    $args         = [] ,
    ?bool    $defaultValue = null ,
    string   $strategy     = HttpParamStrategy::BOTH ,
    bool     $throwable    = false
)
:?bool
{
    $value = getParam( $request , $name , $args , $strategy , $throwable ) ;
    return match ( $value )
    {
        Boolean::TRUE  => true  ,
        Boolean::FALSE => false ,
        default        => $defaultValue ,
    };
}