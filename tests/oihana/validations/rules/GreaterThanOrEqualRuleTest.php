<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\GreaterThanOrEqualRule;
use PHPUnit\Framework\TestCase;

use Somnambulist\Components\Validation\Factory;

class GreaterThanOrEqualRuleTest extends TestCase
{
    private Factory $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new Factory();
        $this->validator->addRule('gte_field', new GreaterThanOrEqualRule());
    }

    public function testPassesWhenValueIsGreaterThanComparisonField(): void
    {
        $data = [
            'minimumPasswordLength' => 12,
            'requiredPasswordLength' => 8,
        ];

        $validation = $this->validator->validate($data, [
            'minimumPasswordLength' => 'required|integer|gte_field:requiredPasswordLength',
        ]);

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    public function testPassesWhenValueIsEqualToComparisonField(): void
    {
        $data = [
            'minimumPasswordLength' => 8,
            'requiredPasswordLength' => 8,
        ];

        $validation = $this->validator->validate($data, [
            'minimumPasswordLength' => 'required|integer|gte_field:requiredPasswordLength',
        ]);

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    public function testFailsWhenValueIsLessThanComparisonField(): void
    {
        $data = [
            'minimumPasswordLength' => 6,
            'requiredPasswordLength' => 8,
        ];

        $validation = $this->validator->validate($data, [
            'minimumPasswordLength' => 'required|integer|gte_field:requiredPasswordLength',
        ]);

        $this->assertTrue($validation->fails());
        $this->assertFalse($validation->passes());
        $this->assertArrayHasKey('minimumPasswordLength', $validation->errors()->toArray());
    }

    public function testWorksWithFloatValues(): void
    {
        $data = [
            'value1' => 3.15,
            'value2' => 3.14,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testWorksWithNumericStrings(): void
    {
        $data = [
            'value1' => '600',
            'value2' => '300',
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testFailsWithNullValue(): void
    {
        $data = [
            'value1' => null,
            'value2' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'present|gte_field:value2',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testFailsWithNullComparisonValue(): void
    {
        $data = [
            'value1' => 300,
            'value2' => null,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testFailsWithNonNumericValue(): void
    {
        $data = [
            'value1' => 'not-a-number',
            'value2' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testFailsWithNonNumericComparisonValue(): void
    {
        $data = [
            'value1' => 300,
            'value2' => 'not-a-number',
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testWorksWithNegativeNumbers(): void
    {
        $data = [
            'value1' => -50,
            'value2' => -100,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testWorksWithZero(): void
    {
        $data = [
            'value1' => 100,
            'value2' => 0,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testHasCorrectErrorMessage(): void
    {
        $data = [
            'value1' => 100,
            'value2' => 200,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|integer|gte_field:value2',
        ]);

        $errors = $validation->errors();
        $message = $errors->first('value1');

        $this->assertStringContainsString('value1', $message);
        $this->assertStringContainsString('value2', $message);
    }

    public function testWorksWithScientificNotation(): void
    {
        $data = [
            'value1' => '2e2',  // 200
            'value2' => '1e2',  // 100
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|gte_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testPassesWhenValueIsGreaterThanFixedValue(): void
    {
        $data = [
            'timeout' => 900,
        ];

        $validation = $this->validator->validate($data, [
            'timeout' => 'required|integer|gte_field:600',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testPassesWhenValueIsEqualToFixedValue(): void
    {
        $data = [
            'timeout' => 600,
        ];

        $validation = $this->validator->validate($data, [
            'timeout' => 'required|integer|gte_field:600',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testFailsWhenValueIsLessThanFixedValue(): void
    {
        $data = [
            'timeout' => 300,
        ];

        $validation = $this->validator->validate($data, [
            'timeout' => 'required|integer|gte_field:600',
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testWorksWithFixedFloatValue(): void
    {
        $data = [
            'rate' => 1.5,
        ];

        $validation = $this->validator->validate($data, [
            'rate' => 'required|gte_field:0.5',
        ]);

        $this->assertTrue($validation->passes());
    }
}