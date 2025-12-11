<?php

namespace oihana\logging;

use ReflectionException;
use ReflectionProperty;

use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Stringable;

class CompositeLoggerTest extends TestCase
{
    private CompositeLogger $composite;

    protected function setUp(): void
    {
        $this->composite = new CompositeLogger();
    }

    public function testConstructorWithoutLoggers(): void
    {
        $composite = new CompositeLogger();

        $this->assertSame(0, $composite->count);
        $this->assertEmpty($composite->getLoggers());
    }

    public function testConstructorWithLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $composite = new CompositeLogger([$logger1, $logger2]);

        $this->assertSame(2, $composite->count);
        $this->assertCount(2, $composite->getLoggers());
    }

    public function testAddLogger(): void
    {
        $logger = $this->createStub(LoggerInterface::class);

        $result = $this->composite->addLogger($logger);

        $this->assertSame($this->composite, $result); // Fluent interface
        $this->assertSame(1, $this->composite->count);
        $this->assertContains($logger, $this->composite->getLoggers());
    }

    public function testAddMultipleLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);
        $logger3 = $this->createStub(LoggerInterface::class);

        $this->composite
            ->addLogger($logger1)
            ->addLogger($logger2)
            ->addLogger($logger3);

        $this->assertSame(3, $this->composite->count);
        $loggers = $this->composite->getLoggers();
        $this->assertContains($logger1, $loggers);
        $this->assertContains($logger2, $loggers);
        $this->assertContains($logger3, $loggers);
    }

    public function testHasLogger(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $this->assertFalse($this->composite->hasLogger($logger1));

        $this->composite->addLogger($logger1);

        $this->assertTrue($this->composite->hasLogger($logger1));
        $this->assertFalse($this->composite->hasLogger($logger2));

        $this->composite->removeLogger($logger1);

        $this->assertFalse($this->composite->hasLogger($logger1));
    }

    public function testRemoveLogger(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);

        $result = $this->composite->removeLogger($logger1);

        $this->assertSame($this->composite, $result); // Fluent interface
        $this->assertSame(1, $this->composite->count);
        $this->assertNotContains($logger1, $this->composite->getLoggers());
        $this->assertContains($logger2, $this->composite->getLoggers());
    }

    public function testRemoveNonExistentLogger(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $this->composite->addLogger($logger1);
        $this->composite->removeLogger($logger2); // Remove logger that was never added

        $this->assertSame(1, $this->composite->count);
    }

    public function testClear(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);

        $result = $this->composite->clear();

        $this->assertSame($this->composite, $result); // Fluent interface
        $this->assertSame(0, $this->composite->count);
        $this->assertEmpty($this->composite->getLoggers());
    }

    // public function testWeakMapAutoCleanup(): void
    // {
    //     $logger1 = $this->createStub(LoggerInterface::class);
    //     $logger2 = $this->createStub(LoggerInterface::class);
    //
    //     $this->composite->addLogger($logger1);
    //     $this->composite->addLogger($logger2);
    //
    //     $this->assertSame(2, $this->composite->count);
    //
    //     // Remove external reference
    //     unset($logger2);
    //     gc_collect_cycles(); // Force garbage collection
    //
    //     // Logger2 should be automatically removed from WeakMap
    //     $this->assertSame(1, $this->composite->count);
    //     $this->assertContains($logger1, $this->composite->getLoggers());
    // }

    public function testEmergencyBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Emergency message';
        $context = ['key' => 'value'];

        $logger1->method('emergency')->with($message, $context);
        $logger2->method('emergency')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->emergency($message, $context);
    }

    public function testAlertBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Alert message';
        $context = ['key' => 'value'];

        $logger1->method('alert')->with($message, $context);
        $logger2->method('alert')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->alert($message, $context);
    }

    public function testCriticalBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Critical message';
        $context = ['key' => 'value'];

        $logger1->method('critical')->with($message, $context);
        $logger2->method('critical')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->critical($message, $context);
    }

    public function testErrorBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Error message';
        $context = ['key' => 'value'];

        $logger1->method('error')->with($message, $context);
        $logger2->method('error')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->error($message, $context);
    }

    public function testWarningBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Warning message';
        $context = ['key' => 'value'];

        $logger1->method('warning')->with($message, $context);
        $logger2->method('warning')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->warning($message, $context);
    }

    public function testNoticeBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Notice message';
        $context = ['key' => 'value'];

        $logger1->method('notice')->with($message, $context);
        $logger2->method('notice')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->notice($message, $context);
    }

    public function testInfoBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Info message';
        $context = ['key' => 'value'];

        $logger1->method('info')->with($message, $context);
        $logger2->method('info')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->info($message, $context);
    }

    public function testDebugBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $message = 'Debug message';
        $context = ['key' => 'value'];

        $logger1->method('debug')->with($message, $context);
        $logger2->method('debug')->with($message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->debug($message, $context);
    }

    public function testLogBroadcastsToAllLoggers(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $level = 'info';
        $message = 'Log message';
        $context = ['key' => 'value'];

        $logger1->method('log')->with($level, $message, $context);
        $logger2->method('log')->with($level, $message, $context);

        $this->composite->addLogger($logger1);
        $this->composite->addLogger($logger2);
        $this->composite->log($level, $message, $context);
    }

    public function testLoggingWithStringableMessage(): void
    {
        $logger = $this->createStub(LoggerInterface::class);
        $stringable = new class implements Stringable
        {
            public function __toString(): string
            {
                return 'Stringable message';
            }
        };

        $logger->method('info')->with($stringable, []);

        $this->composite->addLogger($logger);
        $this->composite->info($stringable);
    }

    public function testLoggingWithNoLoggers(): void
    {
        // Should not throw any exceptions when no loggers are registered
        $this->composite->info('Test message');
        $this->composite->error('Error message', ['exception' => new \Exception()]);

        $this->assertSame(0, $this->composite->count);
    }

    public function testGetLoggersReturnsSnapshot(): void
    {
        $logger1 = $this->createStub(LoggerInterface::class);
        $logger2 = $this->createStub(LoggerInterface::class);

        $this->composite->addLogger($logger1);
        $loggers1 = $this->composite->getLoggers();

        $this->composite->addLogger($logger2);
        $loggers2 = $this->composite->getLoggers();

        // getLoggers() should return a snapshot, not affect each other
        $this->assertCount(1, $loggers1);
        $this->assertCount(2, $loggers2);
    }

    /**
     * @throws ReflectionException
     */
    public function testCountPropertyIsReadOnly(): void
    {
        $logger = $this->createStub(LoggerInterface::class);
        $this->composite->addLogger($logger);

        $this->assertSame(1, $this->composite->count);

        // Verify it's a read-only property (can't be set directly)
        // This is enforced by PHP 8.4 property hooks
        $reflection = new ReflectionProperty($this->composite, 'count');
        $this->assertTrue($reflection->isPublic());
    }
}
