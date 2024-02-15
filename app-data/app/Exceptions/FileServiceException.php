<?php

declare(strict_types=1);

namespace App\Exceptions;

class FileServiceException extends \Exception
{
    public function __construct(string $message) {
        $this->message = $message;
    }
}
