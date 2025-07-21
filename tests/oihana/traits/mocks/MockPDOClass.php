<?php

namespace oihana\traits\mocks;

use oihana\traits\PDOTrait;
use Psr\Log\LoggerInterface;

class MockPDOClass
{
    public function __construct()
    {

    }

    /**
     * To allow altering results (no operation here).
     */
    public function alter( mixed $document ) :mixed
    {
        return $document;
    }

    use PDOTrait;

}