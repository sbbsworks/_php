<?php

declare(strict_types=1);

namespace App\Providers\Notifiers;

interface IEmailNotifier
{
    public function notify(object $params): object;
}