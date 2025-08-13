<?php

namespace oihana\db\mysql\enums;

use oihana\reflect\traits\ConstantsTrait;

/**
 * The mysql specific parameters.
 */
class MysqlParam
{
    use ConstantsTrait ,
        MysqlParamTrait ;
}