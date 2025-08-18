<?php

namespace oihana\routes\http;

use oihana\enums\http\HttpMethod;

class PutRoute extends MethodRoute
{
    const string INTERNAL_METHOD = HttpMethod::put ;
}