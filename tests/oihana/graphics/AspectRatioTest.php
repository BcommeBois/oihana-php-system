<?php

namespace oihana\graphics;

use PHPUnit\Framework\TestCase;

class AspectRatioTest extends TestCase
{
    public function testConstructorAndGcd()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->assertEquals(1920, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
        $this->assertEquals(120, $ratio->gcd); // 1920 et 1080 -> GCD 120
    }

    public function testUnlockedChangeWidth()
    {
        $ratio = new AspectRatio(1920, 1080);
        $ratio->width = 1280;

        $this->assertEquals(1280, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
        $this->assertEquals(40, $ratio->gcd);
    }

    public function testUnlockedChangeHeight()
    {
        $ratio = new AspectRatio(1920, 1080);
        $ratio->height = 720;

        $this->assertEquals(1920 , $ratio->width  ) ;
        $this->assertEquals(720  , $ratio->height ) ;
        $this->assertEquals(240  , $ratio->gcd    ) ;
    }

    public function testLockAndAdjustWidth()
    {
        $ratio = new AspectRatio(1920, 1080, true);

        $this->assertTrue($ratio->isLocked());
        $this->assertEquals(1920, $ratio->width);
        $this->assertEquals(1080, $ratio->height);

        $ratio->width = 1280;
        $this->assertEquals(1280, $ratio->width);
        $this->assertEquals(720, $ratio->height);
    }

    public function testLockAndAdjustHeight()
    {
        $ratio = new AspectRatio(1920, 1080, true);
        $ratio->height = 540;

        $this->assertEquals(960, $ratio->width);
        $this->assertEquals(540, $ratio->height);
    }

    public function testManualLock()
    {
        $ratio = new AspectRatio(1280, 1024);
        $this->assertFalse($ratio->isLocked());

        $ratio->lock();
        $this->assertTrue($ratio->isLocked());

        $ratio->width = 640;
        $this->assertEquals(640, $ratio->width);
        $this->assertEquals(intval(640 * ($ratio->height / $ratio->width)), $ratio->height);
    }

    public function testUnlock()
    {
        $ratio = new AspectRatio(1920, 1080, true);
        $this->assertTrue($ratio->isLocked());

        $ratio->unlock();
        $this->assertFalse($ratio->isLocked());

        $ratio->width = 1000;
        $this->assertEquals(1000, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
    }
}