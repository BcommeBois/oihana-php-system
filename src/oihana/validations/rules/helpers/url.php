<?php

namespace oihana\validations\rules\helpers;

use oihana\enums\Char;
use oihana\validations\enums\Rules;
use function oihana\core\strings\compile;

/**
 * Generates the 'url[:scheme]' rule expression.
 *
 * The field under this rule must be a valid url format.
 * The default is to validate the common format: any_scheme://.... You can specify specific URL schemes if you wish.
 *
 * Example:
 * ```php
 * $validation = new Factory()->validate( $inputs ,
 * [
 *     'random_url' => 'url' ,          // value can be `any_scheme://...`
 *     'https_url' => 'url:http' ,      // value must be started with `https://`
 *     'http_url' => 'url:http,https' , // value must be started with `http://` or `https://`
 *     'ftp_url' => 'url:ftp' ,         // value must be started with `ftp://`
 *     'custom_url' => 'url:custom',   // value must be started with `custom://`
 * ]);
 * ```
 *
 * @param null|array|string $scheme The scheme(s) of the url to evaluates.
 *
 * @return string
 */
function url( null|array|string $scheme = null ) :string
{
    if( is_array( $scheme ) )
    {
        $scheme = empty( $scheme ) ? null : compile( $scheme , Char::COMMA ) ;
    }
   return compile( [ Rules::URL , $scheme ] , Char::COLON  ) ;
}