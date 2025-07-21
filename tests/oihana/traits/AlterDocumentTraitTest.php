<?php

namespace oihana\traits ;

use DI\DependencyException;
use DI\NotFoundException;
use oihana\enums\Alter;
use oihana\traits\mocks\MockAlterDocument;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

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
            'status' => [Alter::VALUE, 'active']
        ]);

        $input = ['status' => 'pending'];
        $output = $processor->process($input);

        $this->assertSame('active', $output['status']);
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
}