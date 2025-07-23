<?php

namespace oihana\abstracts\mocks;

use oihana\abstracts\Options;

class MockOptions extends Options
{
    public string $foo = '' ;
    public bool   $bar = false ;
    public array  $baz = [] ;

    public function __toString(): string
    {
        return 'OptionsToString';
    }
}