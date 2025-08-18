<?php

namespace oihana\controllers\traits ;

use oihana\controllers\enums\ControllerParam;
use oihana\enums\http\HttpHeader;
use oihana\enums\JsonParam;
use oihana\files\enums\FileMimeType;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;

use function oihana\core\json\isValidJsonEncodeFlags;

trait JsonTrait
{
    /**
     * The default json options used in the controller.
     * @var int
     */
    protected int $jsonOptions = JsonParam::JSON_NONE ;

    /**
     * Initialize the internal $jsonOptions property.
     * @param array $init
     * @param ContainerInterface|null $container
     * @return static
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function initializeJsonOptions( array $init = [] , ?ContainerInterface $container = null  ):static
    {
        $flags = $init[ ControllerParam::JSON_OPTIONS ] ?? null;

        if( $flags == null && $container instanceof ContainerInterface && $container->has( ControllerParam::JSON_OPTIONS ) )
        {
            $flags = (int) $container->get( ControllerParam::JSON_OPTIONS ) ;
        }

        $this->jsonOptions = isValidJsonEncodeFlags( $flags ) ? $flags : JsonParam::JSON_NONE ;

        return $this ;
    }

    /**
     * Return a JSON response
     * @param Response $response
     * @param mixed $data
     * @param int $status
     * @return Response
     */
    public function jsonResponse( Response $response , mixed $data = null , int $status = 200 ): Response
    {
        $response->getBody()->write( json_encode( $data , $this->jsonOptions ) ) ;
        return $response->withStatus( $status )
                        ->withHeader( HttpHeader::CONTENT_TYPE , FileMimeType::JSON ) ;
    }
}