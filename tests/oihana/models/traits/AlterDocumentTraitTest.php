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

    /// ------- URL


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
    public function testUrlSimplePath()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/products']
        ]);

        $input = ['id' => 42, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/products/42', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithCustomProperty()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/users', 'slug']
        ]);

        $input = ['id' => 123, 'slug' => 'john-doe', 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/users/john-doe', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithTrailingSlash()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/api', 'id', null, true]
        ]);

        $input = ['id' => 99, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/api/99/', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithEmptyPath()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '', 'id']
        ]);

        $input = ['id' => 42, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('42', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithLeadingSlash()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/api/v1/products', 'id']
        ]);

        $input = ['id' => 123, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/api/v1/products/123', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithObject()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/items', 'id']
        ]);

        $input = new stdClass();
        $input->id = 55;
        $input->url = '';

        $output = $processor->process($input);

        $this->assertSame('/items/55', $output->url);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithNumericProperty()
    {
        $processor = new MockAlterDocument
        ([
            'url' => [Alter::URL, '/posts', 'postId']
        ]);

        $input = ['postId' => 1001, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/posts/1001', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithStringProperty()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/categories', 'slug']
        ]);

        $input = ['id' => 1, 'slug' => 'electronics', 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/categories/electronics', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithMissingProperty()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/items', 'code']
        ]);

        $input = ['id' => 42, 'url' => '']; // 'code' is missing
        $output = $processor->process($input);

        // Property value becomes empty string when not found
        $this->assertSame('/items', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithTrailingSlashAlreadyPresent()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/api/', 'id', null, true]
        ]);

        $input = ['id' => 88, 'url' => ''];
        $output = $processor->process($input);

        // joinPaths handles duplicate slashes
        $this->assertSame('/api/88/', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlSequentialArray()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/products', 'id']
        ]);

        $input = [
            ['id' => 1, 'url' => ''],
            ['id' => 2, 'url' => ''],
            ['id' => 3, 'url' => ''],
        ];

        $output = $processor->process($input);

        $this->assertSame('/products/1', $output[0]['url']);
        $this->assertSame('/products/2', $output[1]['url']);
        $this->assertSame('/products/3', $output[2]['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithNullProperty()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/items', 'id']
        ]);

        $input = ['id' => null, 'url' => ''];
        $output = $processor->process($input);

        // null becomes empty string
        $this->assertSame('/items', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithZeroProperty()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/index', 'id']
        ]);

        $input = ['id' => 0, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/index/0', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithContainerBaseUrl()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/products', 'id', 'baseUrl']
        ]);

        $processor->container->set('baseUrl', 'https://api.example.com');

        $input = ['id' => 42, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('https://api.example.com/products/42', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithContainerBaseUrlAndTrailingSlash()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/api', 'id', 'baseUrl', true]
        ]);

        $processor->container->set('baseUrl', 'https://example.com');

        $input = ['id' => 123, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('https://example.com/api/123/', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithContainerBaseUrlMissing()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/products', 'id', 'baseUrl']
        ]);

        // Container doesn't have 'baseUrl', falls back to path only
        $input = ['id' => 99, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('/products/99', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithContainerBaseUrlCustomKey()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/items', 'id', 'apiBaseUrl']
        ]);

        $processor->container->set('apiBaseUrl', 'https://api.service.io');

        $input = ['id' => 77, 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('https://api.service.io/items/77', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithContainerBaseUrlNotString()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/products', 'id', 'baseUrl']
        ]);

        // Set non-string value in container (should be ignored)
        $processor->container->set('baseUrl', 12345);

        $input = ['id' => 50, 'url' => ''];
        $output = $processor->process($input);

        // Falls back to path only since baseUrl is not a string
        $this->assertSame('/products/50', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlWithComplexPath()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/v1/api/resources', 'slug', 'baseUrl', true]
        ]);

        $processor->container->set('baseUrl', 'https://cdn.example.com');

        $input = ['slug' => 'my-resource', 'url' => ''];
        $output = $processor->process($input);

        $this->assertSame('https://cdn.example.com/v1/api/resources/my-resource/', $output['url']);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function testUrlSequentialArrayWithContainerBaseUrl()
    {
        $processor = new MockAlterDocument([
            'url' => [Alter::URL, '/posts', 'id', 'baseUrl']
        ]);

        $processor->container->set('baseUrl', 'https://blog.example.com');

        $input = [
            ['id' => 1, 'url' => ''],
            ['id' => 2, 'url' => ''],
        ];

        $output = $processor->process($input);

        $this->assertSame('https://blog.example.com/posts/1', $output[0]['url']);
        $this->assertSame('https://blog.example.com/posts/2', $output[1]['url']);
    }
}