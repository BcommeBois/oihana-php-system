<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\LessThanOrEqualRule;
use PHPUnit\Framework\TestCase;

use Somnambulist\Components\Validation\Factory;

class LessThanOrEqualRuleTest extends TestCase
{
    private Factory $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new Factory();
        $this->validator->addRule('lte_field', new LessThanOrEqualRule() ) ;
    }

    public function testPassesWhenValueIsLessThanComparisonField(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 300,
            'maximumAccessTokenExpiration' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|integer|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertFalse($validation->fails());
        $this->assertTrue($validation->passes());
    }

    public function testPassesWhenValueIsEqualToComparisonField(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 600,
            'maximumAccessTokenExpiration' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|integer|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertFalse($validation->fails());
        $this->assertTrue($validation->passes());
    }

    public function testFailsWhenValueIsGreaterThanComparisonField(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 900,
            'maximumAccessTokenExpiration' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|integer|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertTrue($validation->fails());
        $this->assertFalse($validation->passes());
        $this->assertArrayHasKey('implicitHybridTokenLifetime', $validation->errors()->toArray());
    }

    public function testWorksWithFloatValues(): void
    {
        $data = [
            'value1' => 3.14,
            'value2' => 3.15,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|lte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testWorksWithNumericStrings(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => '300',
            'maximumAccessTokenExpiration' => '600',
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testFailsWithNullValue(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => null,
            'maximumAccessTokenExpiration' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'present|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testFailsWithNullComparisonValue(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 300,
            'maximumAccessTokenExpiration' => null,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testFailsWithNonNumericValue(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 'not-a-number',
            'maximumAccessTokenExpiration' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testFailsWithNonNumericComparisonValue(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 300,
            'maximumAccessTokenExpiration' => 'not-a-number',
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|lte_field:maximumAccessTokenExpiration',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testWorksWithNegativeNumbers(): void
    {
        $data = [
            'value1' => -100,
            'value2' => -50,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|lte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testWorksWithZero(): void
    {
        $data = [
            'value1' => 0,
            'value2' => 100,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|lte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testHasCorrectErrorMessage(): void
    {
        $data = [
            'implicitHybridTokenLifetime' => 900,
            'maximumAccessTokenExpiration' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'implicitHybridTokenLifetime' => 'required|integer|lte_field:maximumAccessTokenExpiration',
        ]);

        $errors = $validation->errors();
        $message = $errors->first('implicitHybridTokenLifetime');

        $this->assertStringContainsString('implicitHybridTokenLifetime', $message);
        $this->assertStringContainsString('maximumAccessTokenExpiration', $message);
    }

    public function testWorksWithScientificNotation(): void
    {
        $data = [
            'value1' => '1e2',  // 100
            'value2' => '2e2',  // 200
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|lte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testPassesWhenValueIsLessThanFixedValue(): void
    {
        $data = [
            'timeout' => 300,
        ];

        $validation = $this->validator->validate($data, [
            'timeout' => 'required|integer|lte_field:600',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testPassesWhenValueIsEqualToFixedValue(): void
    {
        $data = [
            'timeout' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'timeout' => 'required|integer|lte_field:600',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testFailsWhenValueIsGreaterThanFixedValue(): void
    {
        $data = [
            'timeout' => 900,
        ];

        $validation = $this->validator->validate($data, [
            'timeout' => 'required|integer|lte_field:600',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testWorksWithFixedFloatValue(): void
    {
        $data = [
            'rate' => 0.5,
        ];

        $validation = $this->validator->validate($data, [
            'rate' => 'required|lte_field:1.5',
        ]);

        $this->assertTrue($validation->passes());
    }
}