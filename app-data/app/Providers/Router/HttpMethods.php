<?php

declare(strict_types=1);

namespace App\Providers\Router;

enum HttpMethods: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
}
