<?php

namespace oihana\controllers\traits;

use oihana\enums\Output;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait OutputDocumentsTrait
{
    use BaseUrlTrait ,
        StatusTrait ;

    /**
     * The internal documents response returns strategy.
     *
     * @param Request|null $request
     * @param Response|null $response
     * @param array|null $documents
     * @param array $params
     * @param array|null $options
     *
     * @return ?object
     */
    protected function documentsResponse( ?Request $request = null, ?Response $response = null , ?array $documents = null , array $params = [] , ?array $options = null ) :?object
    {
        return $this->success( $request , $response, $documents ,
        [
            Output::COUNT   => is_array( $documents ) ? count( $documents ) : null ,
            Output::OPTIONS => $options ,
            Output::URL     => $this->getDocumentUrl( $request , $params )
        ]);
    }

    /**
     * Invoked in the documentsResponse method to returns the document url.
     *
     * By default, the method use the BaseUrlTrait::getCurrentPath() method, you can overrides it.
     *
     * @param ?Request $request
     * @param array $params
     *
     * @return string
     */
    protected function getDocumentUrl( ?Request $request = null , array $params = [] ) :string
    {
        return $this->getCurrentPath( $request , $params ) ;
    }

    /**
     * Outputs a list of documents.
     * @param ?Request $request
     * @param ?Response|null $response
     * @param ?array $documents
     * @param array $params
     * @param ?array $options
     * @return array|object|null
     */
    protected function outputDocuments
    (
        ?Request  $request   = null,
        ?Response $response  = null ,
        ?array    $documents = null ,
        array     $params    = []   ,
        ?array    $options   = null
    )
    : array|object|null
    {
        if( $response )
        {
            return $this->documentsResponse
            (
                $request ,
                $response ,
                $documents ,
                array_filter( $params , fn( $value ) => !is_null( $value ) ) ,
                $options
            ) ;
        }
        return $documents ;
    }
}