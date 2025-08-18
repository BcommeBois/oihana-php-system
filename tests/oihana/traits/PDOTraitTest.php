<?php

namespace tests\oihana\traits ;

use PDO;
use PDOStatement;

use DI\Container;

use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

use oihana\enums\Param;

use tests\oihana\traits\mocks\MockPDOClass;

class PDOTraitTest extends TestCase
{
    /**
     * @var MockPDOClass
     */
    private MockPDOClass $model;

    protected function setUp(): void
    {
        $this->model = new MockPDOClass();
    }

    /**
     * @throws Exception
     */
    public function testBindValuesWithSimpleBindings(): void
    {
        $mock = $this->createMock(PDOStatement::class);

        $expectedCalls =
        [
            [':id', 123],
            [':name', 'foo']
        ];

        $callIndex = 0;

        $mock->expects($this->exactly(count($expectedCalls)))
            ->method('bindValue')
            ->with( $this->callback(function ($arg1) use (&$callIndex, $expectedCalls)
            {
                return is_string($arg1); // verify the first value only
            }),
            $this->callback(function ($arg2) use (&$callIndex, $expectedCalls)
            {
                return true ; // We won't filter on the 2nd value here, we'll do it lower overall.
            }),
            $this->anything());

        // On surcharge la méthode bindValue pour vérifier les appels un à un
        $mock->method('bindValue')
              ->willReturnCallback(function ($key, $value, $type = null) use (&$callIndex, $expectedCalls)
              {
                $expected = $expectedCalls[$callIndex];
                $this->assertSame($expected[0], $key);
                $this->assertSame($expected[1], $value);
                $callIndex++;
                return true;
            });

        $this->model->bindValues($mock, ['id' => 123, 'name' => 'foo']);
    }

    /**
     * @throws Exception
     */
    public function testBindValuesWithTypedBindings(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        $stmt->expects($this->once())
            ->method('bindValue')
            ->with(':count', 42, PDO::PARAM_INT);

        $this->model->bindValues($stmt, ['count' => [42, PDO::PARAM_INT]]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testFetchReturnsNullIfNoStatement(): void
    {
        $this->model->pdo = null;
        $result = $this->model->fetch('SELECT * FROM table');
        $this->assertNull($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFetchReturnsNullIfExecuteFails(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(false);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->model->pdo = $pdo;
        $result = $this->model->fetch('SELECT * FROM table');
        $this->assertNull($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFetchReturnsResultObject(): void
    {
        $row = ['id' => 1, 'name' => 'test'];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->willReturn(true);
        $stmt->expects($this->once())->method('fetch')->willReturn($row);
        $stmt->expects($this->once())->method('closeCursor');
        $stmt->expects($this->once())->method('setFetchMode');

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->model->pdo = $pdo;

        $result = $this->model->fetch('SELECT * FROM table');

        $this->assertIsObject($result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('test', $result->name);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testFetchAllReturnsEmptyArrayOnNoStatement(): void
    {
        $this->model->pdo = null;
        $result = $this->model->fetchAll('SELECT * FROM table');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFetchAllReturnsResults(): void
    {
        $data = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn($data);
        $stmt->method('closeCursor')->willReturn(true );
        $stmt->method('setFetchMode')->willReturn(true);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->model->pdo = $pdo;

        $result = $this->model->fetchAll('SELECT * FROM table');

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($data, $result);
    }

    /**
     * @throws Exception
     */
    public function testFetchColumnReturnsValue(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchColumn')->with(0)->willReturn(42);
        $stmt->method('closeCursor')->willReturn(true);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->model->pdo = $pdo;

        $result = $this->model->fetchColumn('SELECT COUNT(*) FROM table');
        $this->assertEquals(42, $result);
    }

    /**
     * @throws Exception
     */
    public function testFetchColumnReturnsZeroIfFail(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(false);

        $pdo = $this->createMock(PDO::class);
        $pdo->method('prepare')->willReturn($stmt);

        $this->model->pdo = $pdo;

        $result = $this->model->fetchColumn('SELECT COUNT(*) FROM table');
        $this->assertSame(0, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testInitPdoReturnsPdoFromArray(): void
    {
        $pdo = $this->createMock(PDO::class);

        $this->model->initializePDO([ Param::PDO => $pdo] );

        $result = $this->model->pdo ;

        $this->assertSame($pdo, $result);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testInitPdoReturnsPdoFromContainer(): void
    {
        $container = $this->createMock(Container::class);

        $container->expects($this->once())
            ->method('has')
            ->with('my_pdo')
            ->willReturn(true);

        $pdo = $this->createMock(PDO::class);

        $container->expects($this->once())
            ->method('get')
            ->with('my_pdo')
            ->willReturn($pdo);

        $this->model->initializePDO([ Param::PDO => 'my_pdo'], $container);

        $result = $this->model->pdo ;

        $this->assertSame($pdo, $result);
    }

    /**
     * @throws Exception
     */
    public function testInitializeDefaultFetchModeWithSchema(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        $this->model->schema = self::class;
        $this->model->deferAssignment = true;

        $stmt->expects($this->once())
            ->method('setFetchMode')
            ->with($this->callback(function($mode) {
                return ($mode & PDO::FETCH_CLASS) === PDO::FETCH_CLASS
                    && ($mode & PDO::FETCH_PROPS_LATE) === PDO::FETCH_PROPS_LATE;
            }), self::class);

        $this->model->initializeDefaultFetchMode($stmt);
    }

    /**
     * @throws Exception
     */
    public function testInitializeDefaultFetchModeWithoutSchema(): void
    {
        $stmt = $this->createMock(PDOStatement::class);

        $this->model->schema = null;

        $stmt->expects($this->once())
            ->method('setFetchMode')
            ->with(PDO::FETCH_ASSOC);

        $this->model->initializeDefaultFetchMode($stmt);
    }
}