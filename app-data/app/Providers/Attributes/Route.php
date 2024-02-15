<?php

declare(strict_types=1);

namespace App\Providers\Attributes;

use App\Providers\Router\HttpMethods;

#[\Attribute]
class Route
{
    public string $httpMethod;
    public function __construct(
        public string $path,
        HttpMethods $httpMethod = HttpMethods::GET
    ) {
        $this->httpMethod = $httpMethod->value;
    }
}
