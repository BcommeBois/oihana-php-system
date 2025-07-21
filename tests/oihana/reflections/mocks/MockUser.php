<?php

namespace oihana\reflections\mocks;

class MockUser
{
    public string $name;

    public ?MockAddress $address = null;

    private int $id = 0;

    public function getName(): string {
        return $this->name;
    }

    protected function someProtectedMethod():void
    {

    }
}
