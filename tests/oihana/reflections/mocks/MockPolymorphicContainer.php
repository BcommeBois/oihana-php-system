<?php

namespace oihana\reflections\mocks;

use oihana\reflections\attributes\HydrateWith;

class MockPolymorphicContainer
{
    #[HydrateWith( MockAddress::class, MockUser::class ) ]
    public array $items = [];
}


