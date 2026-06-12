<?php

namespace oihana\controllers\traits;

use Exception;
use oihana\enums\http\CacheControlDirective;
use ZipArchive;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use oihana\controllers\enums\FileResponseOption;
use oihana\enums\http\HttpHeader;
use oihana\files\enums\FileMimeType;

use function oihana\files\assertFile;

trait FileTrait
{
    use StatusTrait ;

    /**
     * Streams a file as a PSR-7 download response.
     *
     * The file is validated with {@see assertFile()} first; a missing or
     * unreadable file is reported as a `500` response (see the `catch` below) rather
     * than leaking warnings. Optional content headers are toggled via `$options`.
     *
     * @param ?Request $request  Optional PSR-7 Request object (used to build the failure response).
     * @param Response $response The PSR-7 Response object to write the file into.
     * @param string   $file     Absolute path of the file to send.
     * @param array    $options  Optional header switches, keyed by {@see FileResponseOption}:
     *                           - `useContentType`        (bool)   add a `Content-Type` header (detected MIME type).
     *                           - `useContentLength`      (bool)   add a `Content-Length` header (file size).
     *                           - `useContentDisposition` (bool)   add a `Content-Disposition` header.
     *                           - `contentDisposition`    (string) the `Content-Disposition` value to use.
     *
     * @return Response The response carrying the file body, or a `500` failure response on error.
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
            assertFile( $file ) ; // throws FileException (extends Exception) on a missing/unreadable file

            $contentDisposition    = $options[ FileResponseOption::CONTENT_DISPOSITION     ] ?? null ;
            $useContentDisposition = $options[ FileResponseOption::USE_CONTENT_DISPOSITION ] ?? null ;
            $useContentLength      = $options[ FileResponseOption::USE_CONTENT_LENGTH      ] ?? null ;
            $useContentType        = $options[ FileResponseOption::USE_CONTENT_TYPE        ] ?? null ;

            if( $useContentType )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_TYPE , mime_content_type( $file ) ) ;
            }

            if( $useContentLength )
            {
                $response = $response->withHeader( HttpHeader::CONTENT_LENGTH , (string) filesize( $file ) ) ;
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
     * Bundles a set of files into a ZIP archive and streams it as a download response.
     *
     * The archive is created at `$archive`, each entry is added from `$path . $name`,
     * then its content is written to the response body and the temporary archive is
     * removed. If the archive cannot be created, a `500` response is returned.
     *
     * @param ?Request $request  Optional PSR-7 Request object (used to build the failure response).
     * @param Response $response The PSR-7 Response object to write the archive into.
     * @param array    $files    List of file names (relative to `$path`) to add to the archive.
     * @param string   $archive  Absolute path of the ZIP archive to create.
     * @param string   $path     Base directory prepended to each entry in `$files`.
     *
     * @return Response|null The response carrying the archive, or a `500` failure response if it could not be opened.
     */
    public function zip
    (
        ?Request $request ,
        Response $response ,
        array    $files ,
        string   $archive ,
        string   $path
    )
    : ?Response
    {
        $zip = new ZipArchive();

        // @-suppressed: open() emits an E_WARNING when the path cannot be opened,
        // but the failure is already surfaced as a 500 response below.
        if ( @$zip->open( $archive , ZIPARCHIVE::CREATE ) !== true  )
        {
            return $this->fail( $request , $response , 500 ,  'zip failed, cannot open the archive path : ' . $archive ) ;
        }

        //add each files of $file_name array to archive
        foreach( $files as $name )
        {
            $zip->addFile( $path . $name , $name );
        }

        $zip->close();

        $response = $response->withHeader( name : HttpHeader::CONTENT_TYPE        , value :  FileMimeType::ZIP )
                             ->withHeader( name : HttpHeader::CONTENT_DISPOSITION , value : 'attachment; filename=' . $archive )
                             ->withHeader( name : HttpHeader::PRAGMA              , value : CacheControlDirective::NO_CACHE )
                             ->withHeader( name : HttpHeader::EXPIRES             , value : '0' ) ;

        $response->getBody()->write( file_get_contents( $archive ) );

        @unlink( $archive ) ; // the archive content is already in the response body; drop the temp file

        return $response ;
    }
}