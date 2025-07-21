<?php

namespace oihana\reflections\mocks;

use oihana\reflections\attributes\HydrateKey;

class MockWithRenameKey
{
    #[HydrateKey('user_name')]
    public ?string $name = null ;
}