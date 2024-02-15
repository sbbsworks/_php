<?php

declare(strict_types=1);

namespace App\Providers\Container;
use App\Exceptions\ContainerException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $entries = [];

    public function get(string $className)
    {
        if($this->has($className)) {
            $entry = $this->entries[$className];
            if(is_callable($entry)) {
                return $entry($this);
            }
            $className = $entry;
        }
        return $this->resolve($className);
    }
    public function has(string $className): bool
    {
        return isset($this->entries[$className]);
    }
    public function set(string $className, callable|string $instanceCallback): Container
    {
        $this->entries[$className] = $instanceCallback;
        return $this;
    }
    private function resolve(string $className)
    {
        $reflectionClass = new \ReflectionClass($className);
        if(!$reflectionClass->isInstantiable()) {
            throw new ContainerException("$className is not instantiable");
        }
        $constructor = $reflectionClass->getConstructor();
        if(!$constructor) {
            return new $className;
        }
        $parameters = $constructor->getParameters();
        if(!$parameters) {
            return new $className;
        }
        $dependencies = array_map(function(\ReflectionParameter $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();
            if(
                $type instanceof \ReflectionNamedType
                && !$type->isBuiltin()
            ) {
                return $this->get($type->getName());
            }
            throw new ContainerException("$name has a wrong type {$type}");
        }, $parameters);
        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
