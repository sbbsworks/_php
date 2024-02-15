<?php

declare(strict_types=1);

namespace App\Exceptions;

class ViewNotFound extends \Exception
{
    protected $message = "View not found";
}
