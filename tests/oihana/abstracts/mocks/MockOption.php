<?php

namespace oihana\abstracts\mocks;

use oihana\abstracts\Option;

class MockOption extends Option
{
    public static function getCommandOption(string $option): string
    {
        return '--' . str_replace('_', '-', $option);
    }
}