<?php

declare(strict_types=1);

namespace App\Providers\Router;

enum RouteActions: string
{
    case Get = 'get';
    case Create = 'create';
    case Update = 'update';
}
