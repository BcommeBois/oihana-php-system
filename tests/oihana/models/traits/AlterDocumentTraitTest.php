<?php

namespace tests\oihana\models\traits ;

use PHPUnit\Framework\TestCase;

use stdClass;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\core\arrays\CleanFlag;
use oihana\models\enums\Alter;

use tests\oihana\models\mocks\MockAlterDocument;

class AlterDocumentTraitTest extends TestCase
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testFloatAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'price' => Alter::FLOAT
        ]);

        $input = [ 'price' => '12.5' ];
        $output = $processor->process( $input );

        $this->assertSame(12.5 , $output[ 'price' ] );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testIntAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'id' => Alter::INT
        ]);

        $input = [ 'id' => '123' ];

        $output = $processor->process( $input );

        $this->assertSame(123 , $output[ 'id' ] );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testIntAlterationDoesNotCastFloat()
    {
        $processor = new MockAlterDocument
        ([
            'id' => Alter::INT
        ]);

        $input = [ 'id' => 123.5 ];
        $output = $processor->process( $input );

        $this->assertSame(123 , $output[ 'id' ] );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testArrayAndCleanAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'tags' => [ Alter::ARRAY, Alter::CLEAN ]
        ]);

        $input = [ 'tags' => 'a;b' ] ;
        $output = $processor->process($input);

        $this->assertSame(['a', 'b'], $output['tags']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testJsonParseAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'meta' => [ Alter::JSON_PARSE ]
        ]);

        $input = [ 'meta' => '{"enabled":true,"count":3}' ];
        $output = $processor->process($input);

        $this->assertIsArray($output);
        $this->assertIsObject($output['meta']);
        $this->assertTrue($output['meta']->enabled );
        $this->assertSame(3, $output['meta']->count );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testJsonParseAlterationAssociative()
    {
        $processor = new MockAlterDocument
        ([
            'meta' => [ Alter::JSON_PARSE , true ]
        ]);

        $input = [ 'meta' => '{"enabled":true,"count":3}' ];
        $output = $processor->process($input);

        $this->assertIsArray($output);
        $this->assertIsArray($output['meta']);
        $this->assertTrue($output['meta']['enabled']);
        $this->assertSame(3, $output['meta']['count']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testJsonStringifyAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'data' => [ Alter::JSON_STRINGIFY ]
        ]);

        $input = [ 'data' => ['a' => 1, 'b' => true] ];

        $output = $processor->process($input);

        $this->assertEquals('{"a":1,"b":true}', $output['data'] );
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testJsonStringifyWithOptionAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'data' => [ Alter::JSON_STRINGIFY, JSON_PRETTY_PRINT ]
        ]);

        $input    = [ 'data' => ['a' => 1 , 'b' => true] ];
        $expected = json_encode( $input['data'], JSON_PRETTY_PRINT );
        $output   = $processor->process($input);

        $this->assertSame($expected , $output['data']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testCallableAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'score' => [Alter::CALL, fn($v) => $v * 2]
        ]);

        $input = ['score' => 10];
        $output = $processor->process($input);

        $this->assertSame(20, $output['score']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testCallableAlterationWithTwoArguments()
    {
        // On définit une fonction qui prend deux arguments
        $processor = new MockAlterDocument([
            'score' => [Alter::CALL, fn($v, $multiplier) => $v * $multiplier, 3]
        ]);

        $input = ['score' => 10];
        $output = $processor->process($input);

        // La fonction devrait multiplier 10 par 3
        $this->assertSame(30, $output['score']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testCallableAlterationWithStringFunctionCallable()
    {
        $processor = new MockAlterDocument([
            'score' => [Alter::CALL, 'oihana\core\numbers\clip', 2 , 5 ] // le 3e paramètre = facteur
        ]);

        $output = $processor->process(['score' => 7]);
        $this->assertSame(5, $output['score']);

        $output = $processor->process(['score' => 1]);
        $this->assertSame(2 , $output['score']);

        $output = $processor->process(['score' => 3]);
        $this->assertSame(3 , $output['score']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testValueAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'status' => [ Alter::VALUE , 'active' ]
        ]);

        $input = [ 'status' => 'pending' ] ;
        $output = $processor->process($input);

        $this->assertSame('active' , $output['status'] ) ;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testSequentialArrayAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'amount' => Alter::FLOAT
        ]);

        $input = [
            ['amount' => '5.1'],
            ['amount' => '2.4'],
        ];

        $output = $processor->process($input);

        $this->assertSame(5.1, $output[0]['amount']);
        $this->assertSame(2.4, $output[1]['amount']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testObjectAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'price' => Alter::FLOAT
        ]);

        $input = new stdClass();
        $input->price = '42.3';

        $output = $processor->process($input);

        $this->assertSame(42.3, $output->price);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUnchangedWhenNoAlters()
    {
        $processor = new MockAlterDocument(); // empty alters

        $input = ['key' => 'value'];
        $output = $processor->process($input);

        $this->assertSame($input, $output);
    }

    // ----- Not

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNotAlterationSingleBoolean()
    {
        $processor = new MockAlterDocument([
            'active' => Alter::NOT
        ]);

        $input = ['active' => true];
        $output = $processor->process($input);

        $this->assertSame(false, $output['active']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNotAlterationArrayOfBooleans()
    {
        $processor = new MockAlterDocument([
            'flags' => Alter::NOT
        ]);

        $input = ['flags' => [true, false, true]];
        $output = $processor->process($input);

        $this->assertSame([false, true, false], $output['flags']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNotAlterationNonBooleanValue()
    {
        $processor = new MockAlterDocument([
            'enabled' => Alter::NOT
        ]);

        $input = ['enabled' => 1]; // truthy value
        $output = $processor->process($input);

        $this->assertSame(false, $output['enabled']);
    }

    // ----- Normalize

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeWithDefaultFlags()
    {
        $processor = new MockAlterDocument([
            'name' => Alter::NORMALIZE
        ]);

        $input = ['name' => '  John  '];
        $output = $processor->process($input);

        $this->assertSame('John', $output['name']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeEmptyString()
    {
        $processor = new MockAlterDocument([
            'description' => Alter::NORMALIZE
        ]);

        $input = ['description' => '   '];
        $output = $processor->process($input);

        $this->assertNull($output['description']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeNullValue()
    {
        $processor = new MockAlterDocument([
            'value' => Alter::NORMALIZE
        ]);

        $input = ['value' => null];
        $output = $processor->process($input);

        $this->assertNull($output['value']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeWithCustomFlags()
    {
        $processor = new MockAlterDocument([
            'status' => [Alter::NORMALIZE, CleanFlag::NULLS]
        ]);

        $input = ['status' => '   '];
        $output = $processor->process($input);

        // With only NULLS flag, whitespace-only strings are NOT treated as empty
        $this->assertSame('   ', $output['status']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeArray()
    {
        $processor = new MockAlterDocument([
            'tags' => Alter::NORMALIZE
        ]);

        $input = ['tags' => ['a', '', null, '  b  ']];
        $output = $processor->process($input);

        // Empty strings and nulls removed, ...
        $this->assertSame(['a', '  b  '], $output['tags']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeNestedArray()
    {
        $processor = new MockAlterDocument([
            'data' => Alter::NORMALIZE
        ]);

        $input = ['data' => [
            'items' => ['a', '', null, 'b'],
            'count' => 2
        ]];
        $output = $processor->process($input);

        // Recursive cleaning removes empty/null from nested arrays
        $this->assertSame(['items' => ['a', 'b'], 'count' => 2], $output['data']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeEmptyArray()
    {
        $processor = new MockAlterDocument([
            'options' => Alter::NORMALIZE
        ]);

        $input = ['options' => ['', null, '  ']];
        $output = $processor->process($input);

        // Empty array returns null with RETURN_NULL flag
        $this->assertNull($output['options']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeWithFalsyFlag()
    {
        $processor = new MockAlterDocument([
            'count' => [Alter::NORMALIZE, CleanFlag::FALSY]
        ]);

        $input = ['count' => 0];
        $output = $processor->process($input);

        $this->assertNull($output['count']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeWithoutReturnNullFlag()
    {
        $processor = new MockAlterDocument
        ([
            'tags' => [Alter::NORMALIZE, CleanFlag::DEFAULT ]
        ]);

        $input = ['tags' => ['', null, '  ']];
        $output = $processor->process($input);

        // Returns empty array instead of null
        $this->assertSame([], $output['tags']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizePreservesValidValues()
    {
        $processor = new MockAlterDocument([
            'email' => Alter::NORMALIZE
        ]);

        $input = ['email' => 'john@example.com'];
        $output = $processor->process($input);

        $this->assertSame('john@example.com', $output['email']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeIntegerNotFalsy()
    {
        $processor = new MockAlterDocument([
            'score' => [Alter::NORMALIZE, CleanFlag::NULLS | CleanFlag::EMPTY]
        ]);

        $input = ['score' => 0];
        $output = $processor->process($input);

        // 0 is preserved without FALSY flag
        $this->assertSame(0, $output['score']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testNormalizeSequentialArray()
    {
        $processor = new MockAlterDocument([
            'name' => Alter::NORMALIZE
        ]);

        $input = [
            ['name' => '  Alice  '],
            ['name' => '   '],
            ['name' => 'Bob'],
        ];

        $output = $processor->process($input);

        $this->assertSame('Alice', $output[0]['name']);
        $this->assertNull($output[1]['name']);
        $this->assertSame('Bob', $output[2]['name']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarString()
    {
        $processor = new MockAlterDocument([
            'name' => Alter::FLOAT
        ]);

        $input = 'just a string';
        $output = $processor->process($input);

        // Scalar value returned unchanged
        $this->assertSame('just a string', $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarInteger()
    {
        $processor = new MockAlterDocument([
            'value' => Alter::FLOAT
        ]);

        $input = 42;
        $output = $processor->process($input);

        // Scalar integer returned unchanged
        $this->assertSame(42, $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarFloat()
    {
        $processor = new MockAlterDocument([
            'amount' => Alter::INT
        ]);

        $input = 3.14;
        $output = $processor->process($input);

        // Scalar float returned unchanged
        $this->assertSame(3.14, $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarBoolean()
    {
        $processor = new MockAlterDocument([
            'active' => Alter::NOT
        ]);

        $output = $processor->process(true);

        // Scalar boolean returned unchanged
        $this->assertSame(true, $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarNull()
    {
        $processor = new MockAlterDocument([
            'value' => Alter::NORMALIZE
        ]);

        $input = null;
        $output = $processor->process($input);

        // Scalar null returned unchanged
        $this->assertNull($output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithMixedArrayContainingScalars()
    {
        $processor = new MockAlterDocument([
            'value' => Alter::FLOAT
        ]);

        // Sequential array with mixed scalar types
        $input = [42, 'string', 3.14, true, null];
        $output = $processor->process($input);

        // Each scalar is processed recursively but remains unchanged
        // since they don't have properties to alter
        $this->assertSame([42, 'string', 3.14, true, null], $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithArrayOfMixedStructures()
    {
        $processor = new MockAlterDocument([
            'id' => Alter::INT
        ]);

        // Sequential array with mixed structures
        $input = [
            ['id' => '1'],
            42,
            'string',
            ['id' => '2'],
            null
        ];

        $output = $processor->process($input);

        // Associative arrays are altered, scalars pass through unchanged
        $this->assertSame(1, $output[0]['id']);
        $this->assertSame(42, $output[1]);
        $this->assertSame('string', $output[2]);
        $this->assertSame(2, $output[3]['id']);
        $this->assertNull($output[4]);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarWhenAltersEmpty()
    {
        $processor = new MockAlterDocument(); // No alters defined

        $input = 'some value';
        $output = $processor->process($input);

        // Scalar returned unchanged even with empty alters
        $this->assertSame('some value', $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testAlterWithScalarResource()
    {
        $processor = new MockAlterDocument([
            'stream' => Alter::FLOAT
        ]);

        $resource = fopen('php://memory', 'r');
        $input = $resource;

        $output = $processor->process($input);

        // Resource returned unchanged
        $this->assertSame($resource, $output);

        fclose($resource);
    }
}