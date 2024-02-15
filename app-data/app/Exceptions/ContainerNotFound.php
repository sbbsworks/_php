<?php

declare(strict_types=1);

namespace App\Exceptions;
use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFound extends \Exception implements NotFoundExceptionInterface{}
