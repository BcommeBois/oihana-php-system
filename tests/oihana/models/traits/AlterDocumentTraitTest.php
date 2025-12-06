<?php

namespace tests\oihana\models\traits ;

use DI\Container;
use PHPUnit\Framework\TestCase;

use ReflectionException;
use stdClass;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\models\enums\Alter;

use tests\oihana\models\mocks\MockAlterDocument;

class AlterDocumentTraitTest extends TestCase
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
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
     * @throws ReflectionException
     *
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
     */
    public function testUnchangedWhenNoAlters()
    {
        $processor = new MockAlterDocument(); // empty alters

        $input = ['key' => 'value'];
        $output = $processor->process($input);

        $this->assertSame($input, $output);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testAlterWithScalarString()
    {
        $processor = new MockAlterDocument
        ([
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
     * @throws ReflectionException
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
     * @throws ReflectionException
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
     * @throws ReflectionException
     * @throws ReflectionException
     */
    public function testAlterWithMixedArrayContainingScalars()
    {
        $processor = new MockAlterDocument
        ([
            'value' => Alter::FLOAT
        ]);

        // Sequential array with mixed scalar types
        $input = [42, 'string', 3.14, true, null];
        $output = $processor->process($input);

        // Each scalar is processed recursively but remains unchanged
        // since they don't have properties to alter
        $this->assertSame([42, 'string', 3.14, true, null], $output);
    }

    // --------- Alter::INT

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testAlterWithArrayOfMixedStructures()
    {
        $processor = new MockAlterDocument
        ([
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



    // --------- Alter::FLOAT

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testAlterWithScalarResource()
    {
        $processor = new MockAlterDocument
        ([
            'stream' => Alter::FLOAT
        ]);

        $resource = fopen('php://memory', 'r');
        $input = $resource;

        $output = $processor->process($input);

        // Resource returned unchanged
        $this->assertSame($resource, $output);

        fclose($resource);
    }

    // --------- Alter::MAP

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testMapAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'price' =>
            [
                Alter::MAP ,
                function( array|object $document , ?Container $container , string $key, mixed $value, array $params = [] ) :float|int
                {
                    return $value + ( $value * ( $document['vat'] ?? 0 ) );
                }
            ]
        ]);

        $document = [
            'price' => 100 ,
            'vat'   => 0.2
        ];

        $output = $processor->process( $document );

        $this->assertSame(120, (int) $output['price']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testMapAlterationWithKey()
    {
        $processor = new MockAlterDocument
        ([
            'price' =>
            [
                Alter::MAP ,
                function( array|object $document , ?Container $container , string $key, mixed $value, array $params = [] ) :float|int
                {
                    return $document[$key] + ( $value * ( $document['vat'] ?? 0 ) );
                }
            ]
        ]);

        $document =
        [
            'price' => 100 ,
            'vat'   => 0.2
        ];

        $output = $processor->process( $document );

        $this->assertSame(120, (int) $output['price']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testMapOnly()
    {
        $processor = new MockAlterDocument
        ([
            'price' =>
            [
                Alter::MAP ,
                function( array|object &$document , ?Container $container , string $key, mixed $value ) :int|float
                {
                    $document['total'] = $document['price'] + ( $value * ( $document['vat'] ?? 0 ) );
                    return $value ; // do nothing
                }
            ]
        ]);

        $document =
        [
            'price' => 100 ,
            'vat'   => 0.2
        ];

        $output = $processor->process( $document );

        $this->assertSame(100, (int) $output['price']);
        // $this->assertSame(120, (int) $output['total']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     * @throws ReflectionException
     */
    public function testMapWithDefaultParams()
    {
        $processor = new MockAlterDocument
        ([
            'price' =>
            [
                Alter::MAP ,
                function( array|object $document , ?Container $container , string $key, mixed $value, array $params = [] ) :float|int
                {
                    return $document[$key] + ( $value * ( $document['vat'] ?? $params[0] ?? 0 ) );
                },
                0.2 // default
            ]
        ]);

        $document = [ 'price' => 100 , 'vat' => null ] ; // vat is null
        $output = $processor->process( $document );
        $this->assertSame(120, (int) $output['price']);
    }
}