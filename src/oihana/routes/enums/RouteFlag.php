<?php

namespace oihana\routes\enums;

use oihana\reflect\traits\ConstantsTrait;

class RouteFlag
{
    use ConstantsTrait ;

    const string DEFAULT_FLAG        = 'defaultFlag'       ;
    const string HAS_COUNT           = 'hasCount'          ;
    const string HAS_DELETE          = 'hasDelete'         ;
    const string HAS_DELETE_MULTIPLE = 'hasDeleteMultiple' ;
    const string HAS_GET             = 'hasGet'            ;
    const string HAS_LIST            = 'hasList'           ;
    const string HAS_PATCH           = 'hasPatch'          ;
    const string HAS_POST            = 'hasPost'           ;
    const string HAS_PUT             = 'hasPut'            ;
}