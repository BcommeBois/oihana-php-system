<?php

namespace oihana\logging\enums;

use oihana\reflect\traits\ConstantsTrait;

class MonoLogParam
{
    use ConstantsTrait ;

    public const string ALLOW_INLINE_LINE_BREAKS       = 'allowInlineLineBreaks' ;
    public const string IGNORE_EMPTY_CONTEXT_AND_EXTRA = 'ignoreEmptyContextAndExtra' ;
    public const string BUBBLES                        = 'bubbles' ;
    public const string DATE_FORMAT                    = 'dateFormat' ;
    public const string DIR_PERMISSIONS                = 'dirPermissions' ;
    public const string FILE_PERMISSIONS               = 'filePermissions' ;
    public const string FORMAT                         = 'format' ;
    public const string INCLUDE_STACK_TRACES           = 'includeStackTraces' ;
    public const string LEVEL                          = 'level' ;
    public const string MAX_FILES                      = 'maxFiles' ;
    public const string PATTERN                        = 'pattern' ;
}