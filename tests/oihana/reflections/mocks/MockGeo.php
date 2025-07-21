<?php

namespace oihana\reflections\mocks;

use oihana\reflections\attributes\HydrateWith;

class MockGeo
{
    #[HydrateWith( MockAddress::class ) ]
    public array $locations = [];
}
