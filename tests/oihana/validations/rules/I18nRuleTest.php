<?php

namespace tests\oihana\validations\rules;

use oihana\validations\rules\I18nRule;
use PHPUnit\Framework\TestCase;

use Somnambulist\Components\Validation\Factory;

class I18nRuleTest extends TestCase
{
    private Factory $validator;

    protected function setUp(): void
    {
        $this->validator = new Factory();
        $this->validator->addRule('i18n' , new I18nRule(['fr','en']));
    }

    public function testValidI18nField(): void
    {
        $payload =
        [
            'description'  => [ 'fr' => 'Bonjour' , 'en' => 'Hello' ] ,
        ];

        $validation = $this->validator->validate
        (
            $payload,
            [
                'description' => 'required|array|i18n',
            ]
        );

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    public function testInvalidI18nFieldWithExtraLang(): void
    {
        $payload =
        [
            'description'  => [ 'fr' => 'Bonjour' , 'it' => 'Ciao' ] ,
        ];

        $validation = $this->validator->validate
        (
            $payload,
            [
                'description' => 'required|i18n',
            ]
        );

        $this->assertFalse($validation->passes());
        $this->assertTrue($validation->fails());
    }

    public function testI18nFieldWithNullValues(): void
    {
        $payload =
        [
            'description' => ['fr' => null, 'en' => 'Hello'],
        ];

        $validation = $this->validator->validate
        (
            $payload,
            ['description' => 'required|i18n']
        );

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    public function testI18nFieldWithInvalidValueType(): void
    {
        $payload =
        [
            'description' => ['fr' => 'Bonjour', 'en' => 123],
        ];

        $validation = $this->validator->validate
        (
            $payload,
            ['description' => 'required|i18n']
        );

        $this->assertFalse($validation->passes());
        $this->assertTrue($validation->fails());
    }

    public function testI18nFieldAsObject(): void
    {
        $payload =
        [
            'description' => (object)['fr' => 'Bonjour', 'en' => 'Hello']
        ];

        $validation = $this->validator->validate
        (
            $payload,
            ['description' => 'required|i18n']
        );

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    public function testEmptyPayload(): void
    {
        $payload =
        [
            'description' => [] ,
        ];

        $validation = $this->validator->validate(

            $payload,
            ['description' => 'i18n']
        );

        // Empty array is technically valid because no disallowed keys
        $this->assertTrue($validation->passes());
    }

    public function testMultipleFields(): void
    {
        $payload =
        [
            'description' => ['fr' => 'Bonjour', 'en' => 'Hello'],
            'title'       => ['fr' => 'Titre', 'en' => 'Title'],
        ];

        $validation = $this->validator->validate(
            $payload,
            [
                'description' => 'required|i18n',
                'title'       => 'required|i18n',
            ]
        );

        $this->assertTrue($validation->passes());
        $this->assertFalse($validation->fails());
    }

    /** Multiple fields with one invalid */
    public function testMultipleFieldsWithOneInvalid(): void
    {
        $payload = [
            'description' => ['fr' => 'Bonjour', 'en' => 'Hello'],
            'title'       => ['fr' => 'Titre', 'de' => 'Titel'], // 'de' not allowed
        ];

        $validation = $this->validator->validate(
            $payload,
            [
                'description' => 'required|i18n',
                'title'       => 'required|i18n',
            ]
        );

        $this->assertFalse($validation->passes());
        $this->assertTrue($validation->fails());
    }
}