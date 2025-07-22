<?php

namespace oihana\abstracts\mocks;

use oihana\abstracts\Options;
use oihana\interfaces\Optionable;

class MockOptions extends Options implements Optionable
{
    public string $name  = '';
    public int    $count = 0;
    public array  $tags  = [];
    public bool   $force = false;

    public static function getOption( string $name, string $prefix = '--' ) : ?string
    {
        return match( $name )
        {
            'name'  => $prefix . 'name',
            'count' => $prefix . 'count',
            'tags'  => $prefix . 'tag',
            'force' => $prefix . 'force',
            default => null,
        };
    }
}