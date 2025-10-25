<?php

namespace tests\oihana\models\traits ;

use oihana\models\enums\ModelParam;
use oihana\models\traits\SchemaTrait;
use PHPUnit\Framework\TestCase;

class MockSchema
{
    use SchemaTrait ;
}

class SchemaTraitTest extends TestCase
{
    private object $object;

    protected function setUp(): void
    {
        $this->object = new MockSchema() ;
    }

    public function testDefaultSchemaIsNull(): void
    {
        $this->assertNull($this->object->schema, 'Default schema should be null.');
    }

    public function testInitializeSchemaSetsValue(): void
    {
        $expected = 'org\\schema\\Thing';

        $result = $this->object->initializeSchema([ ModelParam::SCHEMA => $expected ]);

        $this->assertSame($this->object, $result, 'Method should return the same instance (fluent interface).');
        $this->assertSame($expected, $this->object->schema, 'Schema property should be set correctly.');
    }

    public function testInitializeSchemaWithoutKeyKeepsNull(): void
    {
        $result = $this->object->initializeSchema([]);

        $this->assertSame($this->object, $result);
        $this->assertNull($this->object->schema, 'Schema should remain null when key is missing.');
    }

    public function testInitializeSchemaOverridesExistingValue(): void
    {
        // Set an initial value
        $this->object->schema = 'org\\schema\\Person';

        $newValue = 'org\\schema\\Organization';
        $this->object->initializeSchema([ ModelParam::SCHEMA => $newValue ]);

        $this->assertSame($newValue, $this->object->schema, 'Existing schema should be overridden.');
    }
}