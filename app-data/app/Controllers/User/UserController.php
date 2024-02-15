<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Providers\Attributes\Get;
use App\Providers\Attributes\Post;
use App\Providers\Attributes\Put;
use App\Providers\Router\RouteActions;
use App\Providers\Router\Routes;
use App\Services\User\UserService;
use App\View\View;

class UserController
{
    public function __construct(private UserService $userService) {}

    #[Get(Routes::Root->value . '/' .  Routes::User->value)]
    public function index(): View
    {
        return $this->userService->index();
    }
    #[Post(Routes::Root->value . '/' .  Routes::User->value . '/' . RouteActions::Create->value)]
    public function create(): void
    {
        $this->userService->create();
    }
    #[Post(Routes::Root->value . '/' .  Routes::User->value . '/' . RouteActions::Update->value)]
    public function update(): void
    {
        $this->userService->update();
    }
}
