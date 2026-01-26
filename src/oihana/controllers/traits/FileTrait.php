<?php

namespace oihana\controllers\traits;

use Exception;
use ZipArchive;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use oihana\enums\http\HttpHeader;
use oihana\files\enums\FileMimeType;

trait FileTrait
{
    use StatusTrait ;

    public ?string $tmpPath = null ;

    /**
     * Returns a file response
     *
     * @param ?Request  $request  Optional PSR-7 Request object.
     * @param Response  $response The PSR-7 Response object.
     * @param string    $file
     * @param array     $options
     *
     * @return Response
     */
    public function fileResponse
    (
        ?Request $request ,
        Response $response ,
        string $file ,
        array $options = []
    )
    : Response
    {
        try
        {
            $contentDisposition    = $options[ 'contentDisposition'    ] ?? null ;
            $useContentDisposition = $options[ 'useContentDisposition' ] ?? null ;
            $useContentLength      = $options[ 'useContentLength'      ] ?? null ;
            $useContentType        = $options[ 'useContentType'        ] ?? null ;

            if( $useContentType )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_TYPE , mime_content_type( $file ) ) ;
            }

            if( $useContentLength )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_LENGTH , filesize( $file ) ) ;
            }

            if( $useContentDisposition )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_DISPOSITION , $contentDisposition ) ;
            }

            $response->getBody()->write( file_get_contents( $file ) );

            return $response ;
        }
        catch( Exception $e )
        {
            return $this->fail( $request , $response ,  500 , $e->getMessage() ) ;
        }
    }

    /**
     * @param ?Request $request Optional PSR-7 Request object.
     * @param Response $response The PSR-7 Response object.
     * @param array $files
     * @param string $archive
     * @param string $path
     * @return Response|null
     */
    public function zip
    (
        ?Request $request ,
        Response $response ,
        array $files ,
        string $archive ,
        string $path
    )
    : ?Response
    {
        $zip = new ZipArchive();

        if ( $zip->open( $archive , ZIPARCHIVE::CREATE ) !== true  )
        {
            return $this->fail( $request , $response , 500 ,  'zip failed, cannot open the archive path : ' . $archive ) ;
        }

        //add each files of $file_name array to archive
        foreach( $files as $name )
        {
            $zip->addFile( $path . $name , $name );
        }

        $zip->close();

        $response = $response->withHeader(  name : HttpHeader::CONTENT_TYPE        , value :  FileMimeType::ZIP )
                              ->withHeader( name : HttpHeader::CONTENT_DISPOSITION , value : 'attachment; filename=' . $archive )
                              ->withHeader( name : HttpHeader::PRAGMA              , value : 'no-cache' )
                              ->withHeader( name : HttpHeader::EXPIRES             , value : '0' ) ;

        $response->getBody()->write( file_get_contents( $archive ) );

        return $response ;
    }
}