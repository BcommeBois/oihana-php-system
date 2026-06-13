<?php

namespace oihana\controllers\traits;

use Exception;
use ZipArchive;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use oihana\controllers\enums\ExtractOption;
use oihana\controllers\enums\FileResponseOption;
use oihana\enums\http\CacheControlDirective;
use oihana\enums\http\HttpHeader;
use oihana\files\enums\CompressionType;
use oihana\files\enums\FileMimeType;
use oihana\files\exceptions\DirectoryException;
use oihana\files\exceptions\FileException;

use function oihana\controllers\helpers\applyContentHeaders;
use function oihana\files\archive\tar\tar;
use function oihana\files\archive\tar\untar;
use function oihana\files\path\canonicalizePath;
use function oihana\files\path\isBasePath;
use function oihana\files\path\joinPaths;

/**
 * Provides helpers to bundle files into an archive (tar/zip) and stream it as a
 * PSR-7 download response, and to extract incoming archives back to disk.
 *
 * The download helpers build the archive then delegate header emission, streaming and
 * temporary-file cleanup to the shared {@see self::archiveDownload()} method. The
 * extraction helpers guard against path traversal (Zip Slip) and decompression bombs.
 *
 * @package oihana\controllers\traits
 */
trait ArchiveTrait
{
    use StatusTrait ;

    /**
     * Extracts a tar archive into a destination directory.
     *
     * Thin wrapper around `oihana\files\archive\tar\untar()`, which already guards against
     * path traversal and decompression bombs (see {@see TarOption}).
     *
     * @param string $archive Path of the tar archive to extract.
     * @param string $destDir Directory where the archive is extracted.
     * @param array  $options Optional flags, keyed by {@see TarOption}.
     *
     * @return true|array `true` on success, or the list of entries when `dryRun` is enabled.
     *
     * @throws FileException      If the archive is invalid or inaccessible.
     * @throws DirectoryException If the destination cannot be created or written.
     */
    public function extractTar( string $archive , string $destDir , array $options = [] ) : true|array
    {
        return untar( $archive , $destDir , $options ) ;
    }

    /**
     * Extracts a ZIP archive into a destination directory, guarding against path traversal
     * (Zip Slip) and decompression bombs.
     *
     * @param string $archive Path of the ZIP archive to extract.
     * @param string $destDir Directory where the archive is extracted.
     * @param array  $options Optional flags, keyed by {@see ExtractOption}:
     *                        - `maxEntries` (int)  reject archives with more entries.
     *                        - `maxSize`    (int)  reject archives whose total uncompressed size exceeds it.
     *                        - `overwrite`  (bool) allow overwriting existing files (default false).
     *
     * @return string[] The list of extracted entry names (relative to the archive root).
     *
     * @throws FileException      If the archive cannot be opened, an entry escapes the destination,
     *                            a bomb guard trips, or a target exists without `overwrite`.
     * @throws DirectoryException If the destination directory cannot be created.
     */
    public function extractZip( string $archive , string $destDir , array $options = [] ) : array
    {
        $maxEntries = $options[ ExtractOption::MAX_ENTRIES ] ?? null ;
        $maxSize    = $options[ ExtractOption::MAX_SIZE    ] ?? null ;
        $overwrite  = $options[ ExtractOption::OVERWRITE   ] ?? false ;

        $zip = new ZipArchive() ;

        // @-suppressed: open() emits an E_WARNING on a bad archive; the error is surfaced below.
        if( @$zip->open( $archive ) !== true )
        {
            throw new FileException( sprintf( 'Cannot open the zip archive "%s".' , $archive ) ) ;
        }

        if( is_int( $maxEntries ) && $zip->numFiles > $maxEntries )
        {
            $zip->close() ;
            throw new FileException( sprintf( 'The zip archive has too many entries (%d > %d).' , $zip->numFiles , $maxEntries ) ) ;
        }

        if( is_int( $maxSize ) )
        {
            $total = 0 ;
            for( $i = 0 ; $i < $zip->numFiles ; $i++ )
            {
                $total += (int) ( $zip->statIndex( $i )[ 'size' ] ?? 0 ) ;
            }
            if( $total > $maxSize )
            {
                $zip->close() ;
                throw new FileException( sprintf( 'The zip archive exceeds the maximum extracted size (%d > %d bytes).' , $total , $maxSize ) ) ;
            }
        }

        if( !is_dir( $destDir ) && !@mkdir( $destDir , 0o775 , true ) )
        {
            $zip->close() ;
            throw new DirectoryException( sprintf( 'The destination directory "%s" cannot be created.' , $destDir ) ) ;
        }

        $base      = canonicalizePath( $destDir ) ;
        $extracted = [] ;

        for( $i = 0 ; $i < $zip->numFiles ; $i++ )
        {
            $name   = $zip->getNameIndex( $i ) ;
            $target = canonicalizePath( joinPaths( $destDir , $name ) ) ;

            if( !isBasePath( $base , $target ) )
            {
                $zip->close() ;
                throw new FileException( sprintf( 'Zip Slip detected: the entry "%s" escapes the destination directory.' , $name ) ) ;
            }

            if( str_ends_with( $name , '/' ) ) // directory entry
            {
                @mkdir( $target , 0o775 , true ) ;
                continue ;
            }

            if( !$overwrite && file_exists( $target ) )
            {
                $zip->close() ;
                throw new FileException( sprintf( 'The target file "%s" already exists.' , $target ) ) ;
            }

            @mkdir( dirname( $target ) , 0o775 , true ) ;
            file_put_contents( $target , $zip->getFromIndex( $i ) ) ;
            $extracted[] = $name ;
        }

        $zip->close() ;

        return $extracted ;
    }

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
        $response = applyContentHeaders( $response , $produced , $contentType , $options )
                    ->withHeader( HttpHeader::PRAGMA  , CacheControlDirective::NO_CACHE )
                    ->withHeader( HttpHeader::EXPIRES , '0' ) ;

        $response->getBody()->write( file_get_contents( $produced ) ) ;

        @unlink( $produced ) ; // the archive content is already in the response body; drop the temp file

        return $response ;
    }
}
