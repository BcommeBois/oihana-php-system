<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\PostalCodeRule;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Somnambulist\Components\Validation\Exceptions\ParameterException;

final class PostalCodeRuleTest extends TestCase
{
    /**
     * @throws ParameterException
     */
    #[DataProvider('validPostalCodes')]
    public function testValidPostalCode( string|array $country, string $code)
    {
        $rule = new PostalCodeRule()->country($country);
        $this->assertTrue
        (
            $rule->check($code) ,
            sprintf( "Failed asserting that %s is valid for %s." , $code , json_encode( $country ) )
        );
    }

    /**
     * @throws ParameterException
     */
    #[DataProvider('invalidPostalCodes')]
    public function testInvalidPostalCode( string|array $country, mixed $code)
    {
        $rule = new PostalCodeRule()->country( $country ) ;
        $this->assertFalse
        (
            $rule->check( (string) $code ) ,
            sprintf( "Failed asserting that %s is invalid for %s'." , $code , json_encode($country) )
        );
    }

    public static function validPostalCodes(): array
    {
        return [
            ['FR', '75015'],
            ['US', '90210'],
            ['US', '12345-6789'],
            ['IT', '00100'],
            ['CH', '1000'],
            [['FR','US'], '75015'],
            [['FR','US'], '90210'],
        ];
    }

    public static function invalidPostalCodes(): array
    {
        return [
            ['FR', 'ABCDE'],
            ['US', 'ABCDE'],
            ['IT', '1234'],
            ['CH', '0000'],
            ['DE', 'ABCDE'],
        ];
    }

    /**
     * @throws ParameterException
     */
    public function testDefaultPatternIsUsed()
    {
        $rule = new PostalCodeRule( 'FR' );
        $this->assertTrue($rule->check('75015'));
        $this->assertFalse($rule->check('ABCDE'));
    }

    /**
     * @throws ParameterException
     */
    public function testCountryParameterViaMethod()
    {
        $rule = new PostalCodeRule()->country('US');

        $this->assertTrue($rule->check('90210'));
        $this->assertTrue($rule->check('12345-6789'));
        $this->assertFalse($rule->check('ABCDE'));
    }
}