<?php

declare(strict_types=1);

namespace App\Exceptions;

class NotifierException extends \Exception
{
    public function __construct(string $message, int $code) {
        $this->message = $message;
        $this->code = $code;
    }
}
