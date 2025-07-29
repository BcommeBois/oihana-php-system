<?php

namespace oihana\abstracts\mocks;

use oihana\abstracts\Options;

class TestOptions extends Options
{
    public ?string $host   = null;
    public ?int    $port   = null;
    public array   $flags  = [];
    public ?bool   $debug  = null;
}