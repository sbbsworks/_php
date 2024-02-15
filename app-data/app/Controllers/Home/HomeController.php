<?php

declare(strict_types=1);

namespace App\Controllers\Home;

use App\Providers\Attributes\Get;
use App\Services\Home\HomeService;
use App\View\View;

class HomeController
{
    public function __construct(private HomeService $homeService){}

    #[Get()]
    public function index(): View
    {
        return $this->homeService->index();
    }
}
