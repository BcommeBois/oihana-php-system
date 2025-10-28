<?php

namespace tests\oihana\signal;

use oihana\signals\notices\Payload;
use PHPUnit\Framework\TestCase;
use stdClass;

final class PayloadTest extends TestCase
{
    public function testCanInstantiatePayload()
    {
        $notice = new Payload(
            type: 'info',
            data: [ 'id' => 'foo' , 'name' => 'bar' ] ,
            target: new stdClass(),
            context: ['foo' => 'bar']
        );

        $this->assertSame('info', $notice->type);
        $this->assertInstanceOf(stdClass::class, $notice->target);
        $this->assertSame([ 'id' => 'foo' , 'name' => 'bar' ], $notice->data);
        $this->assertSame(['foo' => 'bar'], $notice->context);
    }

    public function testDefaultValues()
    {
        $notice = new Payload('info' );

        $this->assertSame('info', $notice->type);
        $this->assertNull($notice->data);
        $this->assertNull($notice->target);
        $this->assertSame([], $notice->context);
    }
}