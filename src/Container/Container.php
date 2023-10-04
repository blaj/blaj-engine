<?php

namespace Blaj\PhpEngine\Container;

use Blaj\PhpEngine\Container\Attributes\CollectionOf;
use Blaj\PhpEngine\Container\Attributes\Service;
use Blaj\PhpEngine\Container\Exception\ContainerException;
use Blaj\PhpEngine\Container\Exception\NotFoundException;
use Blaj\PhpEngine\Utils\ReflectionUtils;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class Container implements ContainerInterface
{
    private static string $arrayParameterTypeName = 'array';
    private static string $cacheDirectory = 'cache';

    private array $services = [];
    private array $resolvedServices = [];

    private bool $useCache = false;

    public function __construct(bool $useCache)
    {
        $this->useCache = $useCache;
    }

    public function get(string $id): object
    {
        if (array_key_exists($id, $this->resolvedServices)) {
            return $this->resolvedServices[$id];
        }

        $cachedService = $this->loadFromCache($id);
        if ($cachedService !== null && $this->useCache) {
            return $cachedService;
        }

        if ($this->has($id)) {
            $service = $this->services[$id];

            if (is_callable($service)) {
                $this->resolvedServices[$id] = $service($this);

                return $this->resolvedServices[$id];
            }

            $id = $service;
        }

        $object = $this->resolve($id);
        $this->resolvedServices[$id] = $object;

        if ($this->useCache) {
            $this->saveToCache($id, $object);
        }

        return $object;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    public function set(string $id, callable|string $service): self
    {
        $this->services[$id] = $service;

        return $this;
    }

    public function resolve(string $id): object
    {
        try {
            $reflectionsClass = new ReflectionClass($id);
        } catch (ReflectionException $e) {
            throw new NotFoundException($e->getMessage(), $e->getCode(), $e);
        }

        if (!$reflectionsClass->isInstantiable()) {
            throw new ContainerException(sprintf('Class %s is not instantiable', $id));
        }

        $serviceAttributes = $reflectionsClass->getAttributes(Service::class, ReflectionAttribute::IS_INSTANCEOF);

        if (count($serviceAttributes) === 0) {
            throw new NotFoundException(sprintf('Class %s not has `Service` attribute', $id));
        }

        $constructor = $reflectionsClass->getConstructor();

        if ($constructor === null) {
            return new $id;
        }

        $parameters = $constructor->getParameters();

        if (count($parameters) === 0) {
            return new $id;
        }

        $dependencies = array_map(function (ReflectionParameter $reflectionParameter) use ($id) {
            $name = $reflectionParameter->getName();
            $type = $reflectionParameter->getType();
            $collectionOfAttributes = $reflectionParameter->getAttributes(CollectionOf::class, ReflectionAttribute::IS_INSTANCEOF);
            $isArrayType = strtoupper($type?->getName()) === strtoupper(self::$arrayParameterTypeName);

            if ($type === null) {
                throw new ContainerException(sprintf('Failed to resolve class %s, because param %s is missing a type hint', $id, $name));
            }

            if ($type instanceof ReflectionUnionType) {
                throw new ContainerException(sprintf('Failed to resolve class %s, because param %s is union type', $id, $name));
            }

            if (!$type instanceof ReflectionNamedType) {
                throw new ContainerException(sprintf('Failed to resolve class %s, because param %s is invalid param', $id, $name));
            }

            if ($type->isBuiltin() && !$isArrayType && count($collectionOfAttributes) === 0) {
                throw new ContainerException(sprintf('Failed to resolve class %s, because param %s is simple type', $id, $name));
            }

            try {
                if ($isArrayType) {
                    /** @var CollectionOf $collectionOf */
                    $collectionOf = $collectionOfAttributes[0]->newInstance();

                    return
                        array_map(
                            fn(string $className) => $this->get($className),
                            ReflectionUtils::findAllClassesImplementingInterface(
                                __DIR__ . '/../../src',
                                $collectionOf->interface));
                } else {
                    return $this->get($type->getName());
                }
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
                return null;
            }
        }, $parameters);

        try {
            return $reflectionsClass->newInstanceArgs($dependencies);
        } catch (ReflectionException $e) {
            throw new ContainerException(sprintf('Failed to instantiate %s', $id), $e->getCode(), $e);
        }
    }

    private function loadFromCache(string $id): ?object
    {
        $cacheFile = $this->getCacheFilePath($id);

        if (file_exists($cacheFile)) {
            return include($cacheFile);
        }

        return null;
    }

    private function saveToCache(string $id, object $object): void
    {
        $cacheFile = $this->getCacheFilePath($id);
        $cacheData = '<?php return unserialize(' . var_export(serialize($object), true) . ');';

        $cacheDirectory = dirname($cacheFile);

        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        file_put_contents($cacheFile, $cacheData);
    }

    private function getCacheFilePath(string $id): string
    {
        return __DIR__ . '/../../' . self::$cacheDirectory . '/' . md5($id) . '.php';
    }
}