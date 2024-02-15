<?php

declare(strict_types=1);

namespace App\Services\Home;
use App\View\View;

class HomeService
{
    public function __construct(){}
    public function index(): View
    {
        return View::make(__METHOD__);
    }
}
