<?php

namespace oihana\controllers\traits;

use Exception;
use ZipArchive;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use oihana\controllers\enums\FileResponseOption;
use oihana\enums\http\CacheControlDirective;
use oihana\enums\http\HttpHeader;
use oihana\files\enums\CompressionType;
use oihana\files\enums\FileMimeType;

use function oihana\files\archive\tar\tar;

/**
 * Provides helpers to bundle files into an archive (tar/zip) and stream it as a
 * PSR-7 download response.
 *
 * Both helpers build the archive, then delegate header emission, streaming and
 * temporary-file cleanup to the shared {@see self::archiveDownload()} method.
 *
 * @package oihana\controllers\traits
 */
trait ArchiveTrait
{
    use StatusTrait ;

    /**
     * Bundles files and/or directories into a tar archive (optionally compressed) and
     * streams it as a download response.
     *
     * Delegates the archive creation to `oihana\files\archive\tar\tar()`. Only `GZIP`,
     * `BZIP2` and `NONE` compressions are supported; any other value raises an
     * `UnsupportedCompressionException`, reported as a `500` response.
     *
     * @param ?Request     $request     Optional PSR-7 Request object (used to build the failure response).
     * @param Response     $response    The PSR-7 Response object to write the archive into.
     * @param string|array $paths       File and/or directory path(s) to add to the archive.
     * @param string       $archive     Absolute path of the tar archive to create.
     * @param ?string      $compression One of {@see CompressionType}: `GZIP` (default), `BZIP2` or `NONE`.
     * @param array        $options     Optional header switches (see {@see FileResponseOption}).
     *
     * @return Response The response carrying the archive, or a `500` failure response on error.
     */
    public function tarResponse
    (
        ?Request     $request ,
        Response     $response ,
        string|array $paths ,
        string       $archive ,
        ?string      $compression = CompressionType::GZIP ,
        array        $options = []
    )
    : Response
    {
        try
        {
            $produced = tar( $paths , $archive , $compression ) ;

            $contentType = match( $compression )
            {
                CompressionType::BZIP2 => FileMimeType::TAR_BZ2 ,  // application/x-bzip2
                CompressionType::NONE  => FileMimeType::TAR[ 1 ] , // application/x-tar
                default                => FileMimeType::TAR_GZ ,   // application/gzip (GZIP)
            } ;

            return $this->archiveDownload( $response , $produced , $contentType , $options ) ;
        }
        catch( Exception $e )
        {
            return $this->fail( $request , $response , 500 , $e->getMessage() ) ;
        }
    }

    /**
     * Bundles a set of files into a ZIP archive and streams it as a download response.
     *
     * Each entry is added from `$path . $name`. If the archive cannot be created, a
     * `500` response is returned.
     *
     * @param ?Request $request  Optional PSR-7 Request object (used to build the failure response).
     * @param Response $response The PSR-7 Response object to write the archive into.
     * @param array    $files    List of file names (relative to `$path`) to add to the archive.
     * @param string   $archive  Absolute path of the ZIP archive to create.
     * @param string   $path     Base directory prepended to each entry in `$files`.
     * @param array    $options  Optional header switches (see {@see FileResponseOption}).
     *
     * @return Response The response carrying the archive, or a `500` failure response if it could not be opened.
     */
    public function zipResponse
    (
        ?Request $request ,
        Response $response ,
        array    $files ,
        string   $archive ,
        string   $path ,
        array    $options = []
    )
    : Response
    {
        $zip = new ZipArchive();

        // @-suppressed: open() emits an E_WARNING when the path cannot be opened,
        // but the failure is already surfaced as a 500 response below.
        if ( @$zip->open( $archive , ZIPARCHIVE::CREATE ) !== true )
        {
            return $this->fail( $request , $response , 500 , 'zip failed, cannot open the archive path : ' . $archive ) ;
        }

        foreach( $files as $name )
        {
            $zip->addFile( $path . $name , $name );
        }

        $zip->close();

        return $this->archiveDownload( $response , $archive , FileMimeType::ZIP , $options ) ;
    }

    /**
     * Emits the download headers for a produced archive, streams its content into the
     * response body, then removes the temporary archive file.
     *
     * @param Response $response    The PSR-7 Response object to write the archive into.
     * @param string   $produced    Absolute path of the produced archive file.
     * @param string   $contentType The `Content-Type` to advertise when enabled.
     * @param array    $options     Optional header switches (see {@see FileResponseOption}).
     *
     * @return Response The response carrying the archive body.
     */
    private function archiveDownload( Response $response , string $produced , string $contentType , array $options = [] ) : Response
    {
        $contentDisposition    = $options[ FileResponseOption::CONTENT_DISPOSITION     ] ?? 'attachment; filename=' . basename( $produced ) ;
        $useContentDisposition = $options[ FileResponseOption::USE_CONTENT_DISPOSITION ] ?? true ;
        $useContentLength      = $options[ FileResponseOption::USE_CONTENT_LENGTH      ] ?? true ;
        $useContentType        = $options[ FileResponseOption::USE_CONTENT_TYPE        ] ?? true ;

        if( $useContentType )
        {
            $response = $response->withHeader( HttpHeader::CONTENT_TYPE , $contentType ) ;
        }

        if( $useContentLength )
        {
            $response = $response->withHeader( HttpHeader::CONTENT_LENGTH , (string) filesize( $produced ) ) ;
        }

        if( $useContentDisposition )
        {
            $response = $response->withHeader( HttpHeader::CONTENT_DISPOSITION , $contentDisposition ) ;
        }

        $response = $response->withHeader( HttpHeader::PRAGMA  , CacheControlDirective::NO_CACHE )
                             ->withHeader( HttpHeader::EXPIRES , '0' ) ;

        $response->getBody()->write( file_get_contents( $produced ) ) ;

        @unlink( $produced ) ; // the archive content is already in the response body; drop the temp file

        return $response ;
    }
}
