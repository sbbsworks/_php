<?php
use App\Modules\Module;

require_once __DIR__ . '/../vendor/autoload.php';

use App\App;
use App\View\View;
use App\Exceptions\RouterException;
use App\Exceptions\UnsupportedHttpMethod;
use App\Providers\Router\HttpMethods;

try {
    $httpMethod = HttpMethods::tryFrom(strtolower($_SERVER['REQUEST_METHOD'])) ?? null;
    if(!$httpMethod) {
        throw new UnsupportedHttpMethod();
    }

    echo (new App(
            (new Module()),
            (object)[
                'method' => $httpMethod,
                'uri' => $_SERVER['REQUEST_URI'],
            ]
        ))->run();
} catch (Exception|Error|Throwable $error) {
    if($error instanceof RouterException) {
        $code = $error->getCode();
        $message = $error->getMessage();
        header("HTTP/1.0 $code $message");
        echo View::make("_$code::index", [$message]);
        exit;
    }
    if($error instanceof UnsupportedHttpMethod) {
        header("HTTP/1.0 400 Bad Request");
        echo View::make('_400::index', [$error->getMessage()]);
        exit;
    }
    header("HTTP/1.0 500 Bad Gateway");
    echo View::make('_500::index', [$error->getMessage()]);
}
