<?php

namespace tests\oihana\signal;

use oihana\signals\Notice;

use PHPUnit\Framework\TestCase;
use stdClass;

class NoticeTest extends TestCase
{
    public function testCanInstantiateNotice()
    {
        $notice = new Notice(
            type: 'afterDelete',
            target: new stdClass(),
            context: ['foo' => 'bar']
        );

        $this->assertSame('afterDelete', $notice->type);
        $this->assertInstanceOf(stdClass::class, $notice->target);
        $this->assertSame(['foo' => 'bar'], $notice->context);
    }

    public function testDefaultValues()
    {
        $notice = new Notice('beforeInsert');

        $this->assertSame('beforeInsert', $notice->type);
        $this->assertNull($notice->target);
        $this->assertSame([], $notice->context);
    }

    public function testContextCanBeEmptyArray()
    {
        $notice = new Notice(
            type: 'update',
            target: new stdClass(),
            context: []
        );

        $this->assertSame([], $notice->context);
    }

    public function testTargetCanBeNull()
    {
        $notice = new Notice(
            type: 'delete',
            target: null,
            context: ['deleted' => 1]
        );

        $this->assertNull($notice->target);
        $this->assertSame(['deleted' => 1], $notice->context);
    }
}