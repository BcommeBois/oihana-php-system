<?php

namespace oihana\controllers\enums;

use oihana\controllers\enums\traits\ControllerParamTrait;
use oihana\reflect\traits\ConstantsTrait;

/**
 * The enumeration of all the common controller's parameters.
 */
class ControllerParam
{
    use ConstantsTrait ,
        ControllerParamTrait ;
}