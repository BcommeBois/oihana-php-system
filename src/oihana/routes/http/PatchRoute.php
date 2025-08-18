<?php

namespace oihana\routes\http;

use oihana\enums\http\HttpMethod;

class PatchRoute extends MethodRoute
{
    const string INTERNAL_METHOD = HttpMethod::patch ;
}