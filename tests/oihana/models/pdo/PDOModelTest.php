<?php

namespace oihana\models\pdo;

use DI\Container;
use oihana\enums\Param;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class PDOModelTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        $this->container = new Container() ;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testConstructorInitializesPropertiesAndPdo(): void
    {
        $pdo = $this->createMock(PDO::class ) ;

        // Configure container to return the PDO mock for service 'my_pdo'
        $this->container->set('my_pdo' , $pdo ) ;

        $this->assertTrue( $this->container->has('my_pdo') );

        $init =
        [
            Param::PDO              => 'my_pdo',
            Param::DEFER_ASSIGNMENT => true,
            Param::SCHEMA           => stdClass::class,
            Param::ALTERS           => ['foo' => 'bar'],
            Param::BINDS            => ['baz' => 'qux'],
        ];

        $model = new PDOModel( $this->container, $init );

        $this->assertSame( $this->container, $model->container);
        $this->assertTrue( $model->deferAssignment );
        $this->assertSame( stdClass::class, $model->schema);
        $this->assertEquals(['foo' => 'bar'], $model->alters);
        $this->assertEquals(['baz' => 'qux'], $model->binds);
        $this->assertInstanceOf(PDO::class, $model->pdo);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testFetchReturnsNullWhenNoPdo(): void
    {
        $model = new PDOModel($this->container);
        $model->pdo = null;

        $result = $model->fetch('SELECT 1');
        $this->assertNull($result);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testFetchReturnsResultObject(): void
    {
        $data = ['id' => 123, 'name' => 'Alice'];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('fetch')->willReturn($data);
        $stmt->expects($this->once())->method('closeCursor');
        $stmt->expects($this->once())->method('setFetchMode');

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $model = new PDOModel($this->container);
        $model->pdo = $pdo;

        $result = $model->fetch('SELECT * FROM users WHERE id = :id', ['id' => 123]);

        $this->assertIsObject($result);
        $this->assertEquals(123, $result->id);
        $this->assertEquals('Alice', $result->name);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws Exception
     * @throws ContainerExceptionInterface
     */
    public function testFetchAllReturnsResults(): void
    {
        $rows = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('fetchAll')->willReturn($rows);
        $stmt->expects($this->once())->method('closeCursor');
        $stmt->expects($this->once())->method('setFetchMode');

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $model = new PDOModel($this->container);
        $model->pdo = $pdo;

        $result = $model->fetchAll('SELECT * FROM users');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($rows, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFetchColumnReturnsValue(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('fetchColumn')->with(0)->willReturn(42);
        $stmt->expects($this->once())->method('closeCursor');

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $model = new PDOModel($this->container);
        $model->pdo = $pdo;

        $result = $model->fetchColumn('SELECT COUNT(*) FROM users');
        $this->assertSame(42, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFetchColumnReturnsZeroOnFailure(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(false);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $model = new PDOModel($this->container);
        $model->pdo = $pdo;

        $result = $model->fetchColumn('SELECT COUNT(*) FROM users');
        $this->assertSame(0, $result);
    }
}