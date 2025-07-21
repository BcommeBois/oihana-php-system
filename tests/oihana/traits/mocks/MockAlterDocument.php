<?php

namespace oihana\traits\mocks;

use DI\DependencyException;
use DI\NotFoundException;
use oihana\traits\AlterDocumentTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class MockAlterDocument
{
    public function __construct(array $alters = [])
    {
        $this->alters = $alters;
    }

    use AlterDocumentTrait;

    /**
     * @throws NotFoundExceptionInterface
     * @throws NotFoundException
     * @throws ContainerExceptionInterface
     * @throws DependencyException
     */
    public function process( mixed $input ): mixed
    {
        return $this->alter( $input ) ;
    }
}