<?php

declare(strict_types=1);

namespace App;

use App\Modules\Module;

class App
{
    public function __construct(
        protected Module $module,
        protected object $request
    ){}

    public function run()
    {
        return $this->module->router->resolve(
            $this->request->method,
            $this->request->uri
        );
    }
}
