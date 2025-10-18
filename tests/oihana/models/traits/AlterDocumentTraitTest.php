<?php

namespace oihana\models\traits ;

use PHPUnit\Framework\TestCase;

use stdClass;

use DI\DependencyException;
use DI\NotFoundException;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use oihana\models\enums\Alter;

use tests\oihana\traits\mocks\MockAlterDocument;

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
    public function testUrlAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'url' => [ Alter::URL, '/users/' ]
        ]);

        $input = [ 'id' => 123, 'url' => 123 ];
        $output = $processor->process($input);

        $this->assertSame('/users/123', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithCustomPropertyAlteration()
    {
        $processor = new MockAlterDocument
        ([
            'url' => [ Alter::URL, '/profiles/', 'name' ]
        ]);

        $input = [ 'id' => 123, 'name' => 'John', 'url' => '' ];
        $output = $processor->process($input);

        $this->assertSame('/profiles/John', $output['url']);
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
}
