<?php

declare(strict_types=1);

namespace App\Providers\Router;

enum Routes: string
{
    case Root = '';
    case User = 'user';
    case File = 'file';
    case Notify = 'notify';
}
