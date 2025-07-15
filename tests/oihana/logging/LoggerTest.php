<?php

namespace oihana\logging;

use oihana\enums\Char;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * The Logger unit tests
 * @example
 * ```
 * ./vendor/bin/phpunit ./tests/oihana/logging/LoggerTest.php
 * ```
 */
class LoggerTest extends TestCase
{
    protected string $directory = 'oihana_logs' ;

    private string $logDir;

    protected function setUp(): void
    {
        $this->logDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $this->directory ;
        if ( !file_exists( $this->logDir ) )
        {
            mkdir( $this->logDir , 0777 , true ) ;
        }
    }

    protected function tearDown(): void
    {
        if ( file_exists( $this->logDir ) )
        {
            $files = array_values
            (
                array_filter
                (
                    scandir( $this->logDir ) ,
                    fn( $file ) => $file != Char::DOT && $file != Char::DOUBLE_DOT
                )
            ) ;

            foreach ( $files as $file )
            {
                unlink($this->logDir . DIRECTORY_SEPARATOR . $file ) ;
            }

            rmdir( $this->logDir );
        }
    }

    public function testLoggerCreation(): void
    {
        $logger = new Logger( $this->logDir , Logger::DEBUG ) ;
        $this->assertEquals(Logger::STATUS_LOG_OPEN , $logger->getStatus() );
    }

    public function testLogWritesFile(): void
    {
        $logger = new Logger($this->logDir, Logger::DEBUG);
        $logger->info('Test message');

        $path = $logger->getPath();
        $this->assertFileExists( $path );
        $this->assertStringContainsString('Test message', file_get_contents($path));
    }

    public function testLogRespectsSeverityThreshold(): void
    {
        $logger = new Logger($this->logDir, Logger::WARNING);
        $logger->debug('Should not appear');
        $logger->error('Should appear');

        $logContent = file_get_contents($logger->getPath());

        $this->assertStringNotContainsString('Should not appear', $logContent);
        $this->assertStringContainsString('Should appear', $logContent);
    }

    public function testClearRemovesLogFiles(): void
    {
        $logger = new Logger($this->logDir);
        $logger->info('Something');

        $this->assertFileExists($logger->getPath());

        $logger->clear();

        $this->assertDirectoryIsReadable( $this->logDir ) ;

        $this->assertEmpty( array_diff( scandir( $this->logDir ), [ Char::DOT , Char::DOUBLE_DOT ] ) ) ;
    }

    public function testOnErrorWritesToLog(): void
    {
        $logger = new Logger($this->logDir);
        $logger->onError(E_USER_WARNING, 'Test error', 'file.php', 123);

        $logContent = file_get_contents($logger->getPath());
        $this->assertStringContainsString('Test error', $logContent);
    }

    public function testOnExceptionWritesToLog(): void
    {
        $logger = new Logger($this->logDir);
        $exception = new RuntimeException('Test exception');
        $logger->onException($exception);

        $logContent = file_get_contents($logger->getPath());
        $this->assertStringContainsString('Test exception', $logContent);
        $this->assertStringContainsString('RuntimeException', $logContent);
    }

    public function testGetMessageAndMessages(): void
    {
        $logger = new Logger($this->logDir);
        $messages = $logger->getErrors();

        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);

        $last = $logger->getMessage();
        $this->assertIsString($last);
    }
}
