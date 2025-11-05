<?php

namespace tests\oihana\validations\rules\helpers;

use oihana\validations\enums\Rules;
use PHPUnit\Framework\TestCase;

use function oihana\validations\rules\helpers\after;
use function oihana\validations\rules\helpers\arrayCanOnlyHaveKeys;
use function oihana\validations\rules\helpers\arrayMustHaveKeys;
use function oihana\validations\rules\helpers\before;
use function oihana\validations\rules\helpers\between;
use function oihana\validations\rules\helpers\date;
use function oihana\validations\rules\helpers\defaultValue;
use function oihana\validations\rules\helpers\different;
use function oihana\validations\rules\helpers\digits;
use function oihana\validations\rules\helpers\digitsBetween;
use function oihana\validations\rules\helpers\endsWith;
use function oihana\validations\rules\helpers\extension;
use function oihana\validations\rules\helpers\in;
use function oihana\validations\rules\helpers\length;
use function oihana\validations\rules\helpers\max;
use function oihana\validations\rules\helpers\mimes;
use function oihana\validations\rules\helpers\min;
use function oihana\validations\rules\helpers\notIn;
use function oihana\validations\rules\helpers\prohibitedIf;
use function oihana\validations\rules\helpers\prohibitedUnless;
use function oihana\validations\rules\helpers\regex;
use function oihana\validations\rules\helpers\requiredIf;
use function oihana\validations\rules\helpers\requiredUnless;
use function oihana\validations\rules\helpers\requiredWith;
use function oihana\validations\rules\helpers\requiredWithAll;
use function oihana\validations\rules\helpers\requiredWithout;
use function oihana\validations\rules\helpers\requiredWithoutAll;
use function oihana\validations\rules\helpers\requires;
use function oihana\validations\rules\helpers\rule;
use function oihana\validations\rules\helpers\rules;
use function oihana\validations\rules\helpers\same;
use function oihana\validations\rules\helpers\startsWith;
use function oihana\validations\rules\helpers\url;

final class HelpersTest extends TestCase
{
    public function testRule(): void
    {
        $this->assertEquals
        (
            expected : 'my_rule' ,
            actual   : rule( 'my_rule' ) ,
        );

        $this->assertEquals
        (
            expected : 'my_rule:5' ,
            actual   : rule( 'my_rule' , 5 ) ,
        );

        $this->assertEquals
        (
            expected : 'my_rule:5,hello' ,
            actual   : rule( 'my_rule' , 5 , 'hello' ) ,
        );
    }

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

    public function testArrayCanOnlyHaveKeys(): void
    {
        $this->assertEquals( 'array_can_only_have_keys:foo,bar'  , arrayCanOnlyHaveKeys( 'foo' , 'bar' ) ) ;
    }

    public function testArrayMustHaveKeys(): void
    {
        $this->assertEquals( 'array_must_have_keys:foo,bar'  , arrayMustHaveKeys( 'foo' , 'bar' ) ) ;
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

    public function testDefaultValue(): void
    {
        $this->assertEquals( 'default:1'   , defaultValue(1) ) ;
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

    public function testExtension(): void
    {
        $this->assertEquals( 'extension:jpg,png'  , extension( 'jpg' , 'png' ) ) ;
    }

    public function testMimes(): void
    {
        $this->assertEquals( 'mimes:jpg,png'  , mimes( 'jpg' , 'png' ) ) ;
    }

    public function testIn(): void
    {
        $this->assertEquals( 'in:foo,bar'  , in( 'foo' , 'bar' ) ) ;
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

    public function testNotIn(): void
    {
        $this->assertEquals( 'not_in:foo,bar'  , notIn( 'foo' , 'bar' ) ) ;
    }

    public function testRegex(): void
    {
        $this->assertEquals( 'regex:/(this|that|value)/'  , regex( '/(this|that|value)/' ) ) ;
    }

    public function testProhibitedIf(): void
    {
        $this->assertEquals( 'prohibited_if:password,foo,bar'  , prohibitedIf( 'password' , 'foo' , 'bar' ) ) ;
    }

    public function testProhibitedUnless(): void
    {
        $this->assertEquals( 'prohibited_unless:password,foo,bar'  , prohibitedUnless( 'password' , 'foo' , 'bar'  ) ) ;
    }

    public function testRequiredIf(): void
    {
        $this->assertEquals( 'required_if:name,foo,bar'  , requiredIf( 'name' , 'foo' , 'bar' ) ) ;
    }

    public function testRequiredUnless(): void
    {
        $this->assertEquals( 'required_unless:name,foo,bar'  , requiredUnless( 'name' , 'foo' , 'bar' ) ) ;
    }

    public function testRequiredWith(): void
    {
        $this->assertEquals( 'required_with:email,password'  , requiredWith( 'email' , 'password' ) ) ;
    }

    public function testRequiredWithAll(): void
    {
        $this->assertEquals( 'required_with_all:email,password'  , requiredWithAll( 'email' , 'password' ) ) ;
    }

    public function testRequiredWithout(): void
    {
        $this->assertEquals( 'required_without:email,password'  , requiredWithout( 'email' , 'password' ) ) ;
    }

    public function testRequiredWithoutAll(): void
    {
        $this->assertEquals( 'required_without_all:email,password'  , requiredWithoutAll( 'email' , 'password' ) ) ;
    }

    public function testRequires(): void
    {
        $this->assertEquals( 'requires:email,password'  , requires( 'email' , 'password' ) ) ;
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