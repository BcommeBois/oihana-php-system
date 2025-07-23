<?php

namespace oihana\db\mysql;

use DI\Container;
use PDO;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MysqlModelTest extends TestCase
{
    private ?MysqlModel $model = null ;
    private ?MockObject $pdo   = null ;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $container = $this->createMock(Container::class);

        $this->model = new MysqlModel( $container , [ 'pdo' => $this->pdo ] );
    }

    public function testCreateDatabaseReturnsTrueOnSuccess()
    {
        $this->pdo->expects($this->once())
            ->method('exec')
            ->with($this->stringContains('CREATE DATABASE'))
            ->willReturn(1); // exec retourne le nb de lignes affectées ou true

        $result = $this->model->createDatabase('testdb');
        $this->assertTrue($result);
    }

    public function testCreateDatabaseReturnsFalseOnFailure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid identifier');

        $this->model->createDatabase('invalid-db-name!');
    }
}