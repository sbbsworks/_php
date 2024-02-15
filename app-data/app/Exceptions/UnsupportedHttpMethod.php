<?php

declare(strict_types=1);

namespace App\Exceptions;

class UnsupportedHttpMethod extends \Exception {
    protected $message = "Unsupported http method";
}
