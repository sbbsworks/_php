<?php

declare(strict_types=1);

namespace App\Providers\Router;

use App\Exceptions\RouterException;
use App\Providers\Attributes\Route;
use App\Providers\Container\Container;

class Router
{
    private array $routes;

    public function __construct(private Container $container){}
    public function register(HttpMethods $httpMethod, string $route, array $action): Router
    {
        if(!isset($this->routes[$httpMethod->value])) {
            $this->routes[$httpMethod->value] = [];
        }
        if(isset($this->routes[$httpMethod->value][$route])) {
            $action = implode('::', $action);
            throw new RouterException("500, Duplicate route \"$httpMethod->value $route $action\"", 500);
        }
        $this->routes[$httpMethod->value][$route] = $action;
        return $this;
    }
    public function registerRoutesFromAttributes(array $controllers)
    {
        foreach($controllers as $controller) {
            $reflectionController = new \ReflectionClass($controller);
            foreach($reflectionController->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF);
                foreach($attributes as $attribute) {
                    $route = $attribute->newInstance();
                    $this->register(
                        HttpMethods::tryFrom(strtolower($route->httpMethod)),
                        $route->path,
                        [$controller, $method->getName()]
                    );
                }
            }
        }
        return $this;
    }
    public function resolve(HttpMethods $httpMethod, string $uri)
    {
        $route = explode('?', $uri)[0];
        $action = $this->routes[$httpMethod->value][$route] ?? null;
        if($action && is_array($action)) {
            [$class, $method] = $action;
            if(method_exists($class, $method)) {
                return call_user_func_array([$this->container->get($class), $method], []);
            }
        }
        throw new RouterException('Page not found.', 404);
    }
}
