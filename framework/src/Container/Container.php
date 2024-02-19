<?php

namespace Attinge\Framework\Container;

use Attinge\Framework\Container\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container implements ContainerInterface
{
    private array $services = [];

    /**
     * @throws ContainerException
     */
    public function add(string $id, string|object $service = null) : void
    {
        if (null === $service) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be found");
            }

            $service = $id;
        }

        $this->services[$id] = $service;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerException
     * @throws ContainerExceptionInterface
     * @throws ReflectionException
     */
    public function get(string $id) : object
    {
        if (!$this->has($id)) {
            if (!class_exists($id)) {
                throw new ContainerException("Service $id could not be resolved");
            }

            $this->add($id);
        }

        return $this->resolve($this->services[$id]);
    }

    public function has(string $id) : bool
    {
        return array_key_exists($id, $this->services);
    }
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    private function resolve(string $class) : null|object|string
    {
        $reflectionClass = new ReflectionClass($class);
        $constructor     = $reflectionClass->getConstructor();
        if (null === $constructor) {
            return $reflectionClass->newInstance();
        }

        $constructorParams = $constructor->getParameters();
        $classDependencies = $this->resolveClassDependencies($constructorParams);

        return $reflectionClass->newInstanceArgs($classDependencies);
    }
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerException
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     */
    private function resolveClassDependencies(array $reflectionParameters) : array
    {
        $classDependencies = [];

        /** @var ReflectionParameter $parameter */
        foreach ($reflectionParameters as $parameter) {
            $serviceType         = $parameter->getType();
            $service             = $this->get($serviceType?->getName());
            $classDependencies[] = $service;
        }

        return $classDependencies;
    }
}