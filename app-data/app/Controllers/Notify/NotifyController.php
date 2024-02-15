<?php

declare(strict_types=1);

namespace App\Controllers\Notify;

use App\Providers\Attributes\Get;
use App\Providers\Attributes\Post;
use App\Providers\Router\Routes;
use App\Services\Notify\NotifyService;
use App\View\View;

class NotifyController
{
    public function __construct(private NotifyService $notifyService){}

    #[Get(Routes::Root->value . '/' .  Routes::Notify->value)]
    public function index(): View
    {
        return $this->notifyService->index();
    }
    #[Post(Routes::Root->value . '/' .  Routes::Notify->value)]
    public function notify(): void
    {
        $this->notifyService->notify();
    }
}
