<?php

namespace oihana\routes\http;

use oihana\enums\http\HttpMethod;

class PostRoute extends MethodRoute
{
    const string INTERNAL_METHOD = HttpMethod::post ;
}