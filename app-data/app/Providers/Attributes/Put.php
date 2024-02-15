<?php

declare(strict_types=1);

namespace App\Providers\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class Put extends Route {
    public function __construct(string $path)
    {
        parent::__construct($path, \App\Providers\Router\HttpMethods::PUT);
    }
}
