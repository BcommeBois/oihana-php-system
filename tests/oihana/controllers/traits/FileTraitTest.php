<?php

namespace tests\oihana\controllers\traits;

use oihana\controllers\enums\FileResponseOption;
use oihana\controllers\traits\FileTrait;
use oihana\enums\http\CacheControlDirective;
use oihana\enums\http\HttpHeader;
use oihana\files\enums\CompressionType;
use oihana\files\enums\FileMimeType;

use PHPUnit\Framework\TestCase;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class FileTraitTest extends TestCase
{
    private object $mock;

    private string $dir;
    private string $file;

    protected function setUp(): void
    {
        $this->mock = new class
        {
            use FileTrait;
        };

        $this->dir  = sys_get_temp_dir() . '/file_trait_' . uniqid();
        mkdir( $this->dir );
        $this->file = $this->dir . '/hello.txt';
        file_put_contents( $this->file , 'hello world' );
    }

    protected function tearDown(): void
    {
        foreach ( glob( $this->dir . '/*' ) ?: [] as $f )
        {
            @unlink( $f );
        }
        @rmdir( $this->dir );
    }

    /**
     * A passive Response stub with a writable body.
     */
    private function response(): ResponseInterface
    {
        $stream = $this->createStub( StreamInterface::class );
        $stream->method('write')->willReturnCallback( fn( $data ) => strlen( (string) $data ) );

        $response = $this->createStub( ResponseInterface::class );
        $response->method('getBody')->willReturn( $stream );
        $response->method('withStatus')->willReturnSelf();
        $response->method('withHeader')->willReturnSelf();

        return $response;
    }

    /**
     * A Response stub whose body is writable and whose withHeader() records the
     * headers into $captured (so tests can assert what was emitted).
     */
    private function capturingResponse( array &$captured ): ResponseInterface
    {
        $stream = $this->createStub( StreamInterface::class );
        $stream->method('write')->willReturnCallback( fn( $data ) => strlen( (string) $data ) );

        $response = $this->createStub( ResponseInterface::class );
        $response->method('getBody')->willReturn( $stream );
        $response->method('withStatus')->willReturnSelf();
        $response->method('withHeader')->willReturnCallback
        (
            function( $name , $value ) use ( &$captured , $response )
            {
                $captured[ $name ] = $value ;
                return $response ;
            }
        );

        return $response;
    }

    // ----------------------------------------------------------- fileResponse

    public function testFileResponseWithAllHeaders(): void
    {
        $response = $this->response();

        $result = $this->mock->fileResponse
        (
            null ,
            $response ,
            $this->file ,
            [
                FileResponseOption::USE_CONTENT_TYPE        => true ,
                FileResponseOption::USE_CONTENT_LENGTH      => true ,
                FileResponseOption::USE_CONTENT_DISPOSITION => true ,
                FileResponseOption::CONTENT_DISPOSITION     => 'attachment; filename=hello.txt' ,
            ]
        );

        $this->assertSame( $response , $result );
    }

    public function testFileResponseWithoutOptionalHeaders(): void
    {
        $response = $this->response();

        $result = $this->mock->fileResponse( null , $response , $this->file );

        $this->assertSame( $response , $result );
    }

    public function testFileResponseMissingFileGoesThroughCatch(): void
    {
        // assertFile() throws a FileException (extends Exception) for a missing
        // file, so the catch -> fail() path returns a 500 response cleanly.
        $response = $this->response();

        $result = $this->mock->fileResponse( null , $response , $this->dir . '/nope.txt' );

        $this->assertSame( $response , $result );
    }

    // ----------------------------------------------------------- zip

    public function testZipCreatesArchiveAndSetsHeaders(): void
    {
        $archive = $this->dir . '/bundle.zip';

        $captured = [] ;

        $stream = $this->createStub( StreamInterface::class );
        $stream->method('write')->willReturnCallback( fn( $data ) => strlen( (string) $data ) );

        $response = $this->createStub( ResponseInterface::class );
        $response->method('getBody')->willReturn( $stream );
        $response->method('withHeader')->willReturnCallback
        (
            function( $name , $value ) use ( &$captured , $response )
            {
                $captured[ $name ] = $value ;
                return $response ;
            }
        );

        $result = $this->mock->zip( null , $response , [ 'hello.txt' ] , $archive , $this->dir . '/' );

        $this->assertSame( $response , $result );
        // the archive is streamed into the body then removed (temp cleanup)
        $this->assertFileDoesNotExist( $archive );
        $this->assertSame( CacheControlDirective::NO_CACHE , $captured[ HttpHeader::PRAGMA ] );
    }

    public function testZipFailsWhenArchiveCannotBeOpened(): void
    {
        $response = $this->response();

        // archive path sits *under an existing file*, so ZipArchive::open()
        // returns an error (ENOTDIR) rather than true -> the fail() branch runs.
        $result = $this->mock->zip
        (
            null ,
            $response ,
            [ 'hello.txt' ] ,
            $this->file . '/bundle.zip' ,
            $this->dir . '/'
        );

        $this->assertSame( $response , $result );
    }

    // ----------------------------------------------------------- tarResponse

    public function testTarResponseGzipByDefault(): void
    {
        $captured = [] ;
        $response = $this->capturingResponse( $captured );
        $archive  = $this->dir . '/bundle.tar.gz';

        $result = $this->mock->tarResponse( null , $response , [ $this->file ] , $archive );

        $this->assertSame( $response , $result );
        $this->assertSame( FileMimeType::TAR_GZ , $captured[ HttpHeader::CONTENT_TYPE ] );
        $this->assertArrayHasKey( HttpHeader::CONTENT_LENGTH , $captured );
        $this->assertStringStartsWith( 'attachment;' , $captured[ HttpHeader::CONTENT_DISPOSITION ] );
        $this->assertSame( CacheControlDirective::NO_CACHE , $captured[ HttpHeader::PRAGMA ] );
        $this->assertFileDoesNotExist( $archive ); // temp archive removed after streaming
    }

    public function testTarResponseNone(): void
    {
        $captured = [] ;
        $response = $this->capturingResponse( $captured );

        $this->mock->tarResponse( null , $response , [ $this->file ] , $this->dir . '/plain.tar' , CompressionType::NONE );

        $this->assertSame( FileMimeType::TAR[ 1 ] , $captured[ HttpHeader::CONTENT_TYPE ] ); // application/x-tar
    }

    public function testTarResponseBzip2(): void
    {
        $captured = [] ;
        $response = $this->capturingResponse( $captured );

        $this->mock->tarResponse( null , $response , [ $this->file ] , $this->dir . '/bundle.tar.bz2' , CompressionType::BZIP2 );

        $this->assertSame( FileMimeType::TAR_BZ2 , $captured[ HttpHeader::CONTENT_TYPE ] ); // application/x-bzip2
    }

    public function testTarResponseWithDisabledHeadersAndCustomDisposition(): void
    {
        $captured = [] ;
        $response = $this->capturingResponse( $captured );

        $this->mock->tarResponse
        (
            null ,
            $response ,
            [ $this->file ] ,
            $this->dir . '/custom.tar.gz' ,
            CompressionType::GZIP ,
            [
                FileResponseOption::USE_CONTENT_TYPE   => false ,
                FileResponseOption::USE_CONTENT_LENGTH => false ,
                FileResponseOption::CONTENT_DISPOSITION => 'inline; filename=custom.tar.gz' ,
            ]
        );

        $this->assertArrayNotHasKey( HttpHeader::CONTENT_TYPE   , $captured );
        $this->assertArrayNotHasKey( HttpHeader::CONTENT_LENGTH , $captured );
        $this->assertSame( 'inline; filename=custom.tar.gz' , $captured[ HttpHeader::CONTENT_DISPOSITION ] );
    }

    public function testTarResponseUnsupportedCompressionFails(): void
    {
        $response = $this->response();

        // ZIP is not a tar() compression -> UnsupportedCompressionException -> fail(500)
        $result = $this->mock->tarResponse
        (
            null ,
            $response ,
            [ $this->file ] ,
            $this->dir . '/bad.tar.zip' ,
            CompressionType::ZIP
        );

        $this->assertSame( $response , $result );
    }
}
