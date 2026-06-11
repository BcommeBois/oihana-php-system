<?php

namespace tests\oihana\graphics;

use InvalidArgumentException;

use oihana\graphics\AspectRatio;

use PHPUnit\Framework\TestCase;

class AspectRatioTest extends TestCase
{
    public function testDefaultConstructor()
    {
        $ratio = new AspectRatio();

        $this->assertSame(0, $ratio->width);
        $this->assertSame(0, $ratio->height);
        $this->assertSame(0, $ratio->aspectWidth);
        $this->assertSame(0, $ratio->aspectHeight);
        $this->assertSame(1, $ratio->gcd);
        $this->assertFalse($ratio->locked);
    }

    public function testConstructorAndGcd()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->assertEquals(1920, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
        $this->assertEquals(120, $ratio->gcd); // 1920 et 1080 -> GCD 120
        $this->assertEquals(16, $ratio->aspectWidth);
        $this->assertEquals(9, $ratio->aspectHeight);
        $this->assertFalse($ratio->locked);
    }

    public function testConstructorThrowsOnNegativeWidth()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Width must be greater than or equal to 0.');

        new AspectRatio(-1, 1080);
    }

    public function testConstructorThrowsOnNegativeHeight()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Height must be greater than or equal to 0.');

        new AspectRatio(1920, -1);
    }

    public function testZeroDimensionsAreAccepted()
    {
        $ratio = new AspectRatio(0, 1080);

        $this->assertSame(0, $ratio->width);
        $this->assertSame(1080, $ratio->height);
        $this->assertSame(1, $ratio->gcd);
        $this->assertSame('0:1080', $ratio->ratio());
    }

    public function testConstructorLockNormalizesHeightFromCleanRatio()
    {
        $ratio = new AspectRatio(1920, 1080, true);

        $this->assertTrue($ratio->locked);
        $this->assertEquals(1920, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
        $this->assertEquals(16, $ratio->aspectWidth);
        $this->assertEquals(9, $ratio->aspectHeight);
    }

    public function testRatioString()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->assertSame('16:9', $ratio->ratio());
    }

    public function testToArray()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->assertSame(
        [
            'width'        => 1920,
            'height'       => 1080,
            'aspectWidth'  => 16,
            'aspectHeight' => 9,
            'ratio'        => '16:9',
            'locked'       => false,
        ], $ratio->toArray());
    }

    public function testToStringRepresentation()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->assertSame('1920x1080 (16:9)', (string) $ratio);
    }

    public function testUnlockedChangeWidth()
    {
        $ratio = new AspectRatio(1920, 1080);
        $ratio->width = 1280;

        $this->assertEquals(1280, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
        $this->assertEquals(40, $ratio->gcd);
        $this->assertEquals(32, $ratio->aspectWidth);
        $this->assertEquals(27, $ratio->aspectHeight);
    }

    public function testUnlockedChangeHeight()
    {
        $ratio = new AspectRatio(1920, 1080);
        $ratio->height = 720;

        $this->assertEquals(1920, $ratio->width);
        $this->assertEquals(720, $ratio->height);
        $this->assertEquals(240, $ratio->gcd);
    }

    public function testLockAndAdjustWidth()
    {
        $ratio = new AspectRatio(1920, 1080, true);

        $this->assertTrue($ratio->locked);
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
        $this->assertFalse($ratio->locked);

        $ratio->lock();
        $this->assertTrue($ratio->locked);

        $ratio->width = 640;
        $this->assertEquals(640, $ratio->width);
        $this->assertEquals(512, $ratio->height); // locked at 5:4 -> 640 * 4 / 5
    }

    public function testUnlock()
    {
        $ratio = new AspectRatio(1920, 1080, true);
        $this->assertTrue($ratio->locked);

        $ratio->unlock();
        $this->assertFalse($ratio->locked);

        $ratio->width = 1000;
        $this->assertEquals(1000, $ratio->width);
        $this->assertEquals(1080, $ratio->height);
    }

    public function testLockReturnsFluent()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->assertSame($ratio, $ratio->lock());
        $this->assertSame($ratio, $ratio->unlock());
    }

    public function testSetWidthDirectly()
    {
        $ratio = new AspectRatio(1920, 1080, true);
        $ratio->setWidth(1280);

        $this->assertSame(1280, $ratio->width);
        $this->assertSame(720, $ratio->height);
    }

    public function testSetHeightDirectly()
    {
        $ratio = new AspectRatio(1920, 1080, true);
        $ratio->setHeight(540);

        $this->assertSame(960, $ratio->width);
        $this->assertSame(540, $ratio->height);
    }

    public function testSetWidthThrowsOnNegative()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->expectException(InvalidArgumentException::class);
        $ratio->setWidth(-10);
    }

    public function testSetHeightThrowsOnNegative()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->expectException(InvalidArgumentException::class);
        $ratio->setHeight(-10);
    }

    public function testFromRatio()
    {
        $ratio = AspectRatio::fromRatio(16, 9, 1920);

        $this->assertSame(1920, $ratio->width);
        $this->assertSame(1080, $ratio->height);
        $this->assertSame(16, $ratio->aspectWidth);
        $this->assertSame(9, $ratio->aspectHeight);
        $this->assertTrue($ratio->locked); // locked by default
    }

    public function testFromRatioUnlocked()
    {
        $ratio = AspectRatio::fromRatio(4, 3, 800, false);

        $this->assertSame(800, $ratio->width);
        $this->assertSame(600, $ratio->height);
        $this->assertFalse($ratio->locked);
    }

    public function testFromRatioRoundsHeight()
    {
        // 16/9 * 1001 ≈ 563.06 -> rounds to 563
        $ratio = AspectRatio::fromRatio(16, 9, 1001);

        $this->assertSame(1001, $ratio->width);
        $this->assertSame(563, $ratio->height);
    }

    public function testFromRatioThrowsOnZeroAspectWidth()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Aspect width must be greater than 0.');

        AspectRatio::fromRatio(0, 9, 1920);
    }

    public function testFromRatioThrowsOnNegativeAspectWidth()
    {
        $this->expectException(InvalidArgumentException::class);

        AspectRatio::fromRatio(-16, 9, 1920);
    }

    public function testFromRatioThrowsOnZeroAspectHeight()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Aspect height must be greater than 0.');

        AspectRatio::fromRatio(16, 0, 1920);
    }

    public function testFromRatioThrowsOnNegativeAspectHeight()
    {
        $this->expectException(InvalidArgumentException::class);

        AspectRatio::fromRatio(16, -9, 1920);
    }

    public function testFromRatioThrowsOnZeroWidth()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Width must be greater than 0.');

        AspectRatio::fromRatio(16, 9, 0);
    }

    public function testFromRatioThrowsOnNegativeWidth()
    {
        $this->expectException(InvalidArgumentException::class);

        AspectRatio::fromRatio(16, 9, -1920);
    }

    public function testLockedRatioDoesNotDriftOnRoundedValues()
    {
        // 1001 doesn't divide 16:9 cleanly. Without the snapshot, the
        // simplified aspect would drift to 1001:563 on the first set.
        $ratio = new AspectRatio(1920, 1080, true);

        $ratio->width = 1001;

        $this->assertSame(16, $ratio->aspectWidth);
        $this->assertSame(9, $ratio->aspectHeight);
        $this->assertSame('16:9', $ratio->ratio());
        $this->assertSame(563, $ratio->height);
    }

    public function testNegativeWidthAssignmentThrows()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->expectException(InvalidArgumentException::class);
        $ratio->width = -1;
    }

    public function testNegativeHeightAssignmentThrows()
    {
        $ratio = new AspectRatio(1920, 1080);

        $this->expectException(InvalidArgumentException::class);
        $ratio->height = -1;
    }

    public function testLockedZeroRatioSkipsHeightSynchronization()
    {
        $ratio = new AspectRatio(0, 0, true);

        $this->assertSame(0, $ratio->width);
        $this->assertSame(0, $ratio->height);
    }

    public function testLockedZeroRatioSkipsWidthSynchronization()
    {
        $ratio = new AspectRatio(0, 0, true);

        $ratio->setHeight(10);

        $this->assertSame(0, $ratio->width);
        $this->assertSame(10, $ratio->height);
    }
}
