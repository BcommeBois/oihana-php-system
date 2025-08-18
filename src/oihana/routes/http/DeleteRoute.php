<?php

namespace oihana\routes\http;

use oihana\enums\http\HttpMethod;

class DeleteRoute extends MethodRoute
{
    const string INTERNAL_METHOD = HttpMethod::delete ;
}