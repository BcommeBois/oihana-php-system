<?php

namespace tests\oihana\logging\monolog\processors;

use Monolog\Level;
use Monolog\LogRecord;

use oihana\logging\monolog\processors\SymbolProcessor;

use PHPUnit\Framework\TestCase;

final class SymbolProcessorTest extends TestCase
{
    private function record( Level $level ): LogRecord
    {
        return new LogRecord
        (
            datetime : new \DateTimeImmutable() ,
            channel  : 'test' ,
            level    : $level ,
            message  : 'message' ,
        ) ;
    }

    public function testInvokeAddsColoredSymbolByDefault(): void
    {
        $processor = new SymbolProcessor() ;
        $record    = $processor( $this->record( Level::Error ) ) ;

        $emoji = $record['extra']['level_emoji'] ;

        $this->assertStringContainsString( '✘' , $emoji ) ;
        $this->assertStringContainsString( "\033[31m" , $emoji ) ; // red
        $this->assertStringContainsString( "\033[0m"  , $emoji ) ; // reset
    }

    public function testInvokeWithoutColorsAddsPlainSymbol(): void
    {
        $processor = new SymbolProcessor( false ) ;
        $record    = $processor( $this->record( Level::Info ) ) ;

        $this->assertSame( 'i' , $record['extra']['level_emoji'] ) ;
    }

    public function testInvokeWithEveryKnownLevel(): void
    {
        $processor = new SymbolProcessor( false ) ;

        foreach ( Level::cases() as $level )
        {
            $record = $processor( $this->record( $level ) ) ;
            $this->assertNotSame( '' , $record['extra']['level_emoji'] ) ;
        }
    }
}
