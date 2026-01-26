<?php

namespace oihana\controllers\traits ;

use oihana\enums\Char;
use oihana\enums\http\HttpStatusCode;
use oihana\enums\Output;
use oihana\logging\LoggerTrait;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Provides standardized methods for outputting HTTP status messages and JSON responses.
 *
 * This trait offers:
 * - fail(): to generate structured error responses with logging support.
 * - status(): to generate generic status messages.
 * - success(): to generate success JSON responses, optionally including metadata like count, owner, URL, pagination, etc.
 *
 * Relies on:
 * - BaseUrlTrait: for generating current paths.
 * - JsonTrait: for sending JSON responses.
 * - LoggerTrait: for optional logging of error messages.
 *
 * Usage example:
 * ```php
 * return $this->fail($response, 406, 'Invalid data', ['firstName' => 'required']);
 * return $this->status($response, 'custom message', 200);
 * return $this->success($request, $response, $data, [Output::COUNT => count($data)]);
 * ```
 */
trait StatusTrait
{
    use BaseUrlTrait ,
        JsonTrait    ,
        LoggerTrait  ;

    /**
     * Generates a structured error response with an HTTP status code and optional detailed messages.
     *
     * Automatically logs the error if logging is enabled.
     *
     * @param ?Response       $response The PSR-7 Response object.
     * @param int|string|null $code     The HTTP status code (default: 400).
     * @param ?string         $details  Optional detailed error message to override default description.
     * @param array           $options  Optional array of additional data to include (e.g., errors).
     *
     * @return ?Response Returns a PSR-7 Response object with JSON content or null if $response is not provided.
     *
     * @example
     * ```php
     * return $this->fail(
     *     $response,
     *     406,
     *     'fields validation failed',
     *     [
     *         'firstName' => 'firstName is required',
     *         'lastName'  => 'lastName must be a string'
     *     ]
     * );
     * ```
     */
    public function fail( ?Response $response , string|int|null $code = 400 , ?string $details = null , array $options = [] ) :?Response
    {
        $code       = (int) ( HttpStatusCode::includes( (int) $code ) ? $code : HttpStatusCode::DEFAULT ) ;
        $message    = HttpStatusCode::getDescription( $code ) ;
        $hasDetails = is_string( $details ) && $details != Char::EMPTY ;

        if( $this->loggable )
        {
            $log = implode( Char::SPACE , [ static::class , $code , Char::PIPE , $message ] ) ;
            if( $hasDetails )
            {
                $log .= Char::SPACE . Char::PIPE . Char::SPACE . $details ;
            }
            $this->logger->error( $log ) ;
        }

        if( $hasDetails )
        {
            $options[ Output::DETAILS ] = $details ;
        }

        return $this->status( $response , $message , $code , count($options) > 0 ? $options : null ) ;
    }

    /**
     * Outputs a generic HTTP status message in a JSON response.
     *
     * @param ?Response       $response PSR-7 Response object to send output.
     * @param mixed           $message  The message content.
     * @param int|string|null $code     The HTTP status code (default: 200).
     * @param ?array          $options  Optional array of additional output properties.
     *
     * @return ?Response Returns a PSR-7 Response object with JSON content or null if $response is not provided.
     *
     * @example
     * ```php
     * return $this->status($response, 'bad request', 405);
     * ```
     */
    public function status( ?Response $response , mixed $message = Char::EMPTY , int|string|null $code = 200 , ?array $options = null ) :?Response
    {
        if( isset( $response ) )
        {
            $status = (int) ( $code ?? 200 ) ;
            $type   = HttpStatusCode::getType( $code ) ;

            if( isset( $type ) )
            {
                $output[ Output::STATUS ] = $type ;
            }

            if( isset( $code ) )
            {
                $output[ Output::CODE ] = (int) $code ;
            }

            $output[ Output::MESSAGE ] = $message ;

            if( is_array( $options ) && count( $options ) > 0 )
            {
                $output = [ ...$output , ...$options ] ;
            }

            return $this->jsonResponse( $response , $output , $status ) ;
        }
        return null ;
    }


    /**
     * Outputs a success message with optional JSON metadata.
     *
     * If $response is null, returns the $data directly.
     * Supports optional initialization properties like count, limit, offset, owner, URL, status, total, position, options.
     *
     * @param ?Request  $request  Optional PSR-7 Request object.
     * @param ?Response $response Optional PSR-7 Response object.
     * @param mixed     $data     The main payload or data to return.
     * @param ?array    $init     Optional associative array with keys:
     *                            - count (int): Number of elements
     *                            - limit (int): Pagination limit
     *                            - offset (int): Pagination offset
     *                            - params (array): Parameters for getCurrentPath()
     *                            - status (int): HTTP status code
     *                            - total (int): Total elements
     *                            - url (string): URL to include in response
     *                            - owner (array|object): Owner reference
     *                            - options (array): Additional properties
     *                            - position (int): Optional position in list
     *
     * @return mixed Returns a PSR-7 Response object with JSON if $response is provided, otherwise returns $data directly.
     *
     * @example
     * ```php
     * return $this->success(
     *     $request,
     *     $response,
     *     $data,
     *     [Output::COUNT => count($data), Output::PARAMS => $request->getQueryParams()]
     * );
     * ```
     */
    public function success
    (
        ?Request  $request ,
        ?Response $response ,
        mixed     $data = null ,
        ?array    $init = null ,
    )
    :mixed
    {
        if( isset( $response ) )
        {
            $count    = $init[ Output::COUNT    ] ?? null ;
            $limit    = $init[ Output::LIMIT    ] ?? null ;
            $offset   = $init[ Output::OFFSET   ] ?? null ;
            $params   = $init[ Output::PARAMS   ] ?? [] ;
            $status   = $init[ Output::STATUS   ] ?? 200 ;
            $url      = $init[ Output::URL      ] ?? $this->getCurrentPath( $request , $params ) ;
            $owner    = $init[ Output::OWNER    ] ?? null  ;
            $options  = $init[ Output::OPTIONS  ] ?? null  ;
            $position = $init[ Output::POSITION ] ?? null  ;
            $total    = $init[ Output::TOTAL    ] ?? null  ;

            $output = [ Output::STATUS => Output::SUCCESS ];

            if( is_string( $url ) && $url != Char::EMPTY )
            {
                $output[ Output::URL ] = $url;
            }

            if( is_int( $limit ) )
            {
                $output[ Output::LIMIT ] = $limit ;
            }

            if( is_int( $offset ) )
            {
                $output[ Output::OFFSET ] = $offset ;
            }

            if( is_int( $position ) && $position >= 0 )
            {
                $output[ Output::POSITION ] = $position ;
            }

            if( is_int( $count ) && $count >= 0 )
            {
                $output[ Output::COUNT ] = $count;
            }

            if( is_int( $total ) && $total >= 0 )
            {
                $output[ Output::TOTAL ] = $total ;
            }

            if( $owner )
            {
                $output[ Output::OWNER ] = $owner ;
            }

            if( is_array( $options ) && count( $options ) > 0 )
            {
                $output = [ ...$output , ...$options ] ;
            }

            $output[ Output::RESULT ] = $data ;

            return $this->jsonResponse( $response , $output , $status ) ;
        }

        return $data ;
    }
}