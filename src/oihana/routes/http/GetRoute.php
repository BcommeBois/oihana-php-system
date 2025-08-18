<?php

namespace oihana\routes\http;

use oihana\enums\http\HttpMethod;

class GetRoute extends MethodRoute
{
    const string INTERNAL_METHOD = HttpMethod::get ;
}