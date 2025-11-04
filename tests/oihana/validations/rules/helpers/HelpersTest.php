<?php

namespace tests\oihana\validations\rules\helpers;

use oihana\validations\enums\Rules;
use PHPUnit\Framework\TestCase;

use function oihana\validations\rules\helpers\after;
use function oihana\validations\rules\helpers\before;
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\date;
use function oihana\validations\rules\helpers\different;
use function oihana\validations\rules\helpers\digits;
use function oihana\validations\rules\helpers\digitsBetween;
use function oihana\validations\rules\helpers\endsWith;
use function oihana\validations\rules\helpers\length;
use function oihana\validations\rules\helpers\max;
use function oihana\validations\rules\helpers\min;
use function oihana\validations\rules\helpers\regex;
use function oihana\validations\rules\helpers\rules;
use function oihana\validations\rules\helpers\same;
use function oihana\validations\rules\helpers\startsWith;
use function oihana\validations\rules\helpers\url;

final class HelpersTest extends TestCase
{
    public function testRules(): void
    {
        $this->assertEquals
        (
            expected : 'required|min:5|max:10' ,
            actual   : rules( [ Rules::REQUIRED , min(5) , max(10) ] ) ,
        );

        $this->assertEquals
        (
            expected : 'required|min:5|max:10' ,
            actual   : rules( Rules::REQUIRED , min(5) , max(10) ) ,
        );

        $this->assertEquals
        (
            expected : 'required|min:5|max:10' ,
            actual   : rules( [ Rules::REQUIRED , min(5) ] , max(10) ) ,
        );
    }

    public function testAfter(): void
    {
        $this->assertEquals( 'after:2016-12-31' , after('2016-12-31') ) ;
    }

    public function testBefore(): void
    {
        $this->assertEquals( 'before:2016-12-31' , before('2016-12-31') ) ;
    }

    public function testBetween(): void
    {
        $this->assertEquals( 'between:10,20' , between( 10 , 20 ) ) ;
        $this->assertEquals( 'between:1M,2M' , between( '1M' ,'2M' ) ) ;
    }

    public function testDate(): void
    {
        $this->assertEquals( 'date'   , date() ) ;
        $this->assertEquals( 'date:Y-m-d' , date('Y-m-d' ) ) ;
    }

    public function testDifferent(): void
    {
        $this->assertEquals( 'different:name'  , different( 'name' ) ) ;
    }

    public function testDigits(): void
    {
        $this->assertEquals( 'digits:4'  , digits( 4 ) ) ;
    }

    public function testDigitsBetween(): void
    {
        $this->assertEquals( 'digits_between:2,5'  , digitsBetween( 2 , 5 ) ) ;
    }

    public function testEndsWith(): void
    {
        $this->assertEquals( 'ends_with:suffix'  , endsWith( 'suffix' ) ) ;
    }

    public function testLength(): void
    {
        $this->assertEquals( 'length:10' , length(10   ) ) ;
    }

    public function testMax(): void
    {
        $this->assertEquals( 'max:10' , max(10   ) ) ;
        $this->assertEquals( 'max:2M' , max('2M' ) ) ;
    }

    public function testMin(): void
    {
        $this->assertEquals( 'min:2'  , min(2    ) ) ;
        $this->assertEquals( 'min:1M' , min('1M' ) ) ;
    }

    public function testRegex(): void
    {
        $this->assertEquals( 'regex:/(this|that|value)/'  , regex( '/(this|that|value)/' ) ) ;
    }

    public function testSame(): void
    {
        $this->assertEquals( 'same:password'  , same( 'password' ) ) ;
    }

    public function testStartsWith(): void
    {
        $this->assertEquals( 'starts_with:prefix'  , startsWith( 'prefix' ) ) ;
    }

    public function testUrl(): void
    {
        $this->assertEquals( 'url'  , url() ) ;
        $this->assertEquals( 'url:http'  , url('http') ) ;
        $this->assertEquals( 'url:http,https'  , url('http,https') ) ;
        $this->assertEquals( 'url:http,https'  , url(['http','https']) ) ;
        $this->assertEquals( 'url:ftp'  , url('ftp') ) ;
    }
}