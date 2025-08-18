<?php

namespace oihana\controllers\traits;

use Exception;
use oihana\enums\http\HttpHeader;
use ZipArchive;

use Psr\Http\Message\ResponseInterface as Response;

use oihana\files\enums\FileMimeType;

trait FileTrait
{
    use StatusTrait ;

    public ?string $tmpPath = null ;

    /**
     * Returns a file response
     * @param Response $response
     * @param string $file
     * @param array $options
     * @return Response
     */
    public function fileResponse( Response $response , string $file , array $options = [] ): Response
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
            return $this->fail( $response ,  500 , $e->getMessage() ) ;
        }
    }

    /**
     * @param Response $response
     * @param array $files
     * @param string $archive
     * @param string $path
     * @return Response|null
     */
    public function zip( Response $response , array $files , string $archive , string $path ): ?Response
    {
        $zip = new ZipArchive();

        if ( $zip->open( $archive , ZIPARCHIVE::CREATE ) !== true  )
        {
            return $this->fail( $response , 500 ,  'zip failed, cannot open the archive path : ' . $archive ) ;
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