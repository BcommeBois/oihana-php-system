<?php

namespace oihana\enums ;

use PHPUnit\Framework\TestCase;

class BooleanTest extends TestCase
{
    public function testConstantsExist()
    {
        $this->assertSame('true', Boolean::TRUE);
        $this->assertSame('false', Boolean::FALSE);
    }

    public function testValuesAreStrings()
    {
        $this->assertIsString(Boolean::TRUE );
        $this->assertIsString(Boolean::FALSE );
    }

    public function testTrueIsNotFalse()
    {
        $this->assertNotEquals(Boolean::TRUE, Boolean::FALSE) ;
    }

    public function testGetAllReturnsConstants()
    {
        $all = Boolean::getAll();
        $this->assertArrayHasKey('TRUE', $all);
        $this->assertArrayHasKey('FALSE', $all);
        $this->assertEquals('true', $all['TRUE']);
        $this->assertEquals('false', $all['FALSE']);
    }

    public function testEnumsReturnsSortedUniqueValues()
    {
        $enums = Boolean::enums();
        $this->assertSame(['false', 'true'], $enums);
    }

    public function testGetConstant()
    {
        $constant = Boolean::getConstant( 'true' );
        $this->assertEquals( 'TRUE', $constant);

        $constant = Boolean::getConstant( 'false' );
        $this->assertEquals( 'FALSE', $constant);
    }
}