<?php

declare(strict_types=1);

namespace App\Controllers\File;

use App\Providers\Attributes\Get;
use App\Providers\Attributes\Post;
use App\Providers\Router\RouteActions;
use App\Providers\Router\Routes;
use App\Services\File\FileService;
use App\View\View;

class FileController
{
    public function __construct(private FileService $fileService) {}

    #[Get(Routes::Root->value . '/' .  Routes::File->value)]
    public function index(): View
    {
        return $this->fileService->index();
    }
    #[Get(Routes::Root->value . '/' .  Routes::File->value . '/' . RouteActions::Get->value)]
    public function get(): void
    {
        $this->fileService->get();
    }
    #[Post(Routes::Root->value . '/' .  Routes::File->value)]
    public function create(): void
    {
        $this->fileService->create();
    }
}
