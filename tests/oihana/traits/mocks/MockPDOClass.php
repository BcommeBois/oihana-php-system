<?php

namespace oihana\traits\mocks;

use oihana\models\pdo\PDOTrait;

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