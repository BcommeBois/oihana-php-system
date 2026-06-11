<?php

namespace tests\oihana\logging;

use org\bovigo\vfs\vfsStream;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use oihana\files\exceptions\DirectoryException;
use oihana\logging\enums\LoggerParam;
use oihana\logging\LoggerManager;

use PHPUnit\Framework\TestCase;

/**
 * Concrete LoggerManager used to exercise the abstract base class.
 */
final class MockLoggerManager extends LoggerManager
{
    public function createLogger(): LoggerInterface
    {
        return new NullLogger() ;
    }
}

final class LoggerManagerTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'oihana-logger-manager-' . uniqid() ;
        mkdir( $this->dir , 0o775 , true ) ;
    }

    protected function tearDown(): void
    {
        if ( is_dir( $this->dir ) )
        {
            foreach ( scandir( $this->dir ) as $file )
            {
                if ( $file !== '.' && $file !== '..' )
                {
                    @unlink( $this->dir . DIRECTORY_SEPARATOR . $file ) ;
                }
            }
            @rmdir( $this->dir ) ;
        }
    }

    private function manager( array $init = [] , ?string $name = null ): MockLoggerManager
    {
        return new MockLoggerManager( [ LoggerParam::DIRECTORY => $this->dir ] + $init , $name ) ;
    }

    public function testConstructorAppliesInitOptions(): void
    {
        $manager = new MockLoggerManager
        ([
            LoggerParam::DIRECTORY       => '/var/log' ,
            LoggerParam::EXTENSION       => '.txt' ,
            LoggerParam::PATH            => 'sub' ,
            LoggerParam::DIR_PERMISSIONS => '0755' ,
        ] , 'channel' ) ;

        $this->assertSame( '/var/log' , $manager->directory ) ;
        $this->assertSame( '.txt'     , $manager->extension ) ;
        $this->assertSame( 'sub'      , $manager->path ) ;
        $this->assertSame( 'channel'  , $manager->name ) ;
        $this->assertSame( octdec( '0755' ) , $manager->dirPermissions ) ;
    }

    public function testConstructorAcceptsIntegerDirPermissions(): void
    {
        $manager = new MockLoggerManager( [ LoggerParam::DIR_PERMISSIONS => 0o755 ] ) ;
        $this->assertSame( 0o755 , $manager->dirPermissions ) ;
    }

    public function testCreateLoggerReturnsPsrLogger(): void
    {
        $this->assertInstanceOf( LoggerInterface::class , $this->manager()->createLogger() ) ;
    }

    public function testGetExtensionAndFileNameAndDirectory(): void
    {
        $manager = $this->manager( [ LoggerParam::EXTENSION => '.log' ] , 'app' ) ;

        $this->assertSame( '.log' , $manager->getExtension() ) ;
        $this->assertSame( 'app'  , $manager->getFileName() ) ;
        $this->assertSame( $this->dir , $manager->getDirectory() ) ;
    }

    public function testGetFileNameFallsBackToDefault(): void
    {
        $this->assertSame( LoggerManager::DEFAULT_NAME , $this->manager()->getFileName() ) ;
    }

    public function testGetFilePathBuildsDefaultAndCustomPaths(): void
    {
        $manager = $this->manager( [ LoggerParam::EXTENSION => '.log' ] , 'app' ) ;

        $this->assertSame( $this->dir . DIRECTORY_SEPARATOR . 'app.log' , $manager->getFilePath() ) ;
        $this->assertSame( $this->dir . DIRECTORY_SEPARATOR . 'custom.log' , $manager->getFilePath( 'custom.log' ) ) ;
    }

    public function testClearReturnsFalseWhenFileMissing(): void
    {
        $this->assertFalse( $this->manager()->clear( 'missing.log' ) ) ;
    }

    /**
     * @throws \oihana\files\exceptions\FileException
     */
    public function testClearTruncatesExistingFile(): void
    {
        file_put_contents( $this->dir . DIRECTORY_SEPARATOR . 'app.log' , "line\n" ) ;

        $this->assertTrue( $this->manager( [] , 'app' )->clear( 'app.log' ) ) ;
        $this->assertSame( '' , file_get_contents( $this->dir . DIRECTORY_SEPARATOR . 'app.log' ) ) ;
    }

    /**
     * @throws \oihana\files\exceptions\FileException
     */
    public function testCountLines(): void
    {
        file_put_contents( $this->dir . DIRECTORY_SEPARATOR . 'app.log' , "a\nb\nc\n" ) ;
        $this->assertSame( 3 , $this->manager()->countLines( 'app.log' ) ) ;
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateLogParsesWellFormedLine(): void
    {
        $log = $this->manager()->createLog( '2025-08-21 10:30:00 INFO Some message here' ) ;

        $this->assertNotNull( $log ) ;
        $this->assertSame( '2025-08-21' , $log->date ) ;
        $this->assertSame( '10:30:00'   , $log->time ) ;
        $this->assertSame( 'INFO'       , $log->level ) ;
        $this->assertSame( 'Some message here' , $log->message ) ;
    }

    /**
     * @throws \ReflectionException
     */
    public function testCreateLogReturnsNullForEmptyOrShortLines(): void
    {
        $manager = $this->manager() ;
        $this->assertNull( $manager->createLog( '' ) ) ;
        $this->assertNull( $manager->createLog( 'too short' ) ) ;
    }

    /**
     * @throws \oihana\files\exceptions\FileException
     */
    public function testGetLogLinesReturnsNullWhenFileMissing(): void
    {
        $this->assertNull( $this->manager()->getLogLines( 'missing.log' ) ) ;
    }

    /**
     * @throws \oihana\files\exceptions\FileException
     */
    public function testGetLogLinesParsesFile(): void
    {
        file_put_contents
        (
            $this->dir . DIRECTORY_SEPARATOR . 'app.log' ,
            "2025-08-21 10:30:00 INFO First message\n2025-08-21 10:31:00 ERROR Second message\n"
        ) ;

        $lines = $this->manager( [] , 'app' )->getLogLines( 'app.log' ) ;

        $this->assertIsArray( $lines ) ;
        $this->assertCount( 2 , $lines ) ;
        $this->assertSame( 'INFO'  , $lines[0]->level ) ;
        $this->assertSame( 'ERROR' , $lines[1]->level ) ;
    }

    /**
     * @throws DirectoryException
     */
    public function testGetLoggerFilesListsMatchingFiles(): void
    {
        file_put_contents( $this->dir . DIRECTORY_SEPARATOR . 'app-1.log' , 'x' ) ;
        file_put_contents( $this->dir . DIRECTORY_SEPARATOR . 'app-2.log' , 'x' ) ;
        file_put_contents( $this->dir . DIRECTORY_SEPARATOR . 'other.txt' , 'x' ) ;

        $files = $this->manager( [ LoggerParam::EXTENSION => '.log' ] , 'app' )->getLoggerFiles() ;

        $this->assertContains( 'app-1.log' , $files ) ;
        $this->assertContains( 'app-2.log' , $files ) ;
        $this->assertNotContains( 'other.txt' , $files ) ;
    }

    /**
     * @throws DirectoryException
     */
    public function testEnsureDirectoryCreatesMissingDirectory(): void
    {
        $target  = $this->dir . DIRECTORY_SEPARATOR . 'nested' ;
        $manager = new MockLoggerManager( [ LoggerParam::DIRECTORY => $target ] ) ;

        $manager->ensureDirectory() ;

        $this->assertDirectoryExists( $target ) ;
        @rmdir( $target ) ;
    }

    public function testEnsureDirectoryThrowsWhenCreationFails(): void
    {
        // A file at the parent position makes mkdir() fail (ENOTDIR), even as root.
        $file = $this->dir . DIRECTORY_SEPARATOR . 'a-file' ;
        file_put_contents( $file , 'x' ) ;

        $manager = new MockLoggerManager( [ LoggerParam::DIRECTORY => $file . DIRECTORY_SEPARATOR . 'sub' ] ) ;

        $this->expectException( DirectoryException::class ) ;
        $manager->ensureDirectory() ;
    }

    public function testEnsureDirectoryThrowsWhenNotWritable(): void
    {
        // vfsStream gives a deterministic non-writable directory regardless of uid.
        $root = vfsStream::setup( 'root' ) ;
        vfsStream::newDirectory( 'logs' , 0o444 )->at( $root ) ;

        $manager = new MockLoggerManager( [ LoggerParam::DIRECTORY => vfsStream::url( 'root/logs' ) ] ) ;

        $this->expectException( DirectoryException::class ) ;
        $manager->ensureDirectory() ;
    }
}
