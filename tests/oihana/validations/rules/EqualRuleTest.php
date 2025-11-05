<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\EqualRule;
use PHPUnit\Framework\TestCase;

use Somnambulist\Components\Validation\Factory;

class EqualRuleTest extends TestCase
{
    private Factory $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = new Factory();
        $this->validator->addRule('eq_field', new EqualRule());
    }

    public function testFailsWhenValueIsNotEqualComparisonField(): void
    {
        $data = [
            'minimumPasswordLength'  => 12,
            'requiredPasswordLength' => 8,
        ];

        $validation = $this->validator->validate($data, [
            'minimumPasswordLength' => 'required|integer|eq_field:requiredPasswordLength',
        ]);

        $this->assertTrue($validation->fails());
        $this->assertFalse($validation->passes());
    }

    public function testPassesWhenValueIsEqualToComparisonField(): void
    {
        $data = [
            'minimumPasswordLength'  => 8,
            'requiredPasswordLength' => 8,
        ];

        $validation = $this->validator->validate($data, [
            'minimumPasswordLength' => 'required|integer|eq_field:requiredPasswordLength',
        ]);

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    public function testPassesWithFloatValues(): void
    {
        $data = [
            'value1' => 3.14,
            'value2' => 3.14,
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|eq_field:value2',
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testPassesWithNumericStrings(): void
    {
        $data = [
            'value1' => '600',
            'value2' => '600',
        ];

        $validation = $this->validator->validate($data, [
            'value1' => 'required|eq_field:value2',
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
            'value1' => 'present|eq_field:value2',
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
            'value1' => 'required|eq_field:value2',
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
            'value1' => 'required|eq_field:value2',
        ]);

        $this->assertTrue($validation->fails());
    }
}