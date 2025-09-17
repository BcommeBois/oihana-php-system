<?php

namespace oihana\controllers\traits ;

use oihana\enums\Char;
use oihana\enums\http\HttpStatusCode;
use oihana\enums\Output;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait StatusTrait
{
    use JsonTrait ;

    /**
     * Formats a specific error status message with a code and an errors array representation of all errors.
     * Ex: A 'not acceptable' http request with a failed validation process
     * ```
     * return $this->getError( $response , 406 , 'fields validation failed' , [ 'firstName' => 'firstName is required'  , 'lastName' => 'lastName must be a string' ] ] ) ;
     * ``
     * @param ?Response $response The Response reference.
     * @param int|string|null $code The status code of the response.
     * @param ?string $details The optional error message to overrides the default status message.
     * @param array $options The optional array to inject in the json object (with an errors)
     * @return ?Response
     */
    public function fail( ?Response $response , string|int|null $code = 400 , ?string $details = null , array $options = [] ) :?Response
    {
        $code       = (int) ( HttpStatusCode::includes( (int) $code ) ?? $code ?? HttpStatusCode::DEFAULT ) ;
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
     * Outputs a response status message.
     * @param ?Response $response
     * @param mixed $message The message to send.
     * @param int|string|null $code The status code.
     * @param ?array $options The options to passed-in in the status definition.
     * @return ?Response
     * @example
     * ```
     * return $this->getStatus( $response , 'bad request' , '405' );
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
     * Outputs a success message with a JSON response. If the $response parameter is null, returns the $data parameter value.
     * Ex:
     * return $this->success( $request , $response , $data , [ Output::PARAMS => $request->getParams() ] ) ;
     * @param ?Request $request The HTTP request reference.
     * @param ?Response $response The HTTP Response reference.
     * @param mixed $data The data object to returns (output a JSON object).
     * @param ?array $init An associative definition to initialize the output object with the optional properties :
     * <ul>
     * <i>count (int)  - The optional number of elements.</i>
     * <i>owner (object|array) - The optional owner reference.</i>
     * <i>options (array) - An associative array of optional properties to add in the output object.</i>
     * <i>params (array) - The optional params to passed-in the getCurrentPath() method when the url option is null.</i>
     * <i>status (int) - The optional status of the response.</i>
     * <i>total (int)  - The optional total number of elements.</i>
     * <i>url (string) - The optional url to display.</i>
     * </ul>
     * @return mixed
     */
    public function success
    (
        ?Request  $request ,
        ?Response $response ,
        mixed     $data = null ,
        ?array    $init = null ,
    ) : mixed
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