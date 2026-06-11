<?php

namespace tests\oihana\logging\monolog\processors;

use Monolog\Level;
use Monolog\LogRecord;

use oihana\logging\monolog\processors\EmojiProcessor;

use PHPUnit\Framework\TestCase;

final class EmojiProcessorTest extends TestCase
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

    public function testInvokeAddsEmojiForKnownLevel(): void
    {
        $processor = new EmojiProcessor() ;
        $record    = $processor( $this->record( Level::Error ) ) ;

        $this->assertSame( '❌' , $record['extra']['level_emoji'] ) ;
    }

    public function testInvokeWithEveryKnownLevel(): void
    {
        $processor = new EmojiProcessor() ;

        foreach ( Level::cases() as $level )
        {
            $record = $processor( $this->record( $level ) ) ;
            $this->assertNotSame( '' , $record['extra']['level_emoji'] ) ;
        }
    }
}
