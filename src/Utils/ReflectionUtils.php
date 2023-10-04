<?php

namespace Blaj\PhpEngine\Utils;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;

class ReflectionUtils
{
    public static function findAllClassesImplementingInterface(string $directory, string $interface): array
    {
        $implementingClasses = [];

        $iterator = new RecursiveDirectoryIterator($directory);
        foreach (new RecursiveIteratorIterator($iterator) as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $fileContents = file_get_contents($file->getPathname());

                preg_match('/namespace\s+([a-zA-Z\\\\]+);/', $fileContents, $namespaceMatches);
                preg_match_all('/class\s+(\w+)/', $fileContents, $classMatches);

                if (!empty($namespaceMatches[1]) && !empty($classMatches[1])) {
                    $namespace = $namespaceMatches[1];

                    foreach ($classMatches[1] as $className) {
                        $fullClassName = $namespace . '\\' . $className;

                        if (class_exists($fullClassName)) {
                            $reflectionClass = new ReflectionClass($fullClassName);
                            if ($reflectionClass->implementsInterface($interface)) {
                                $implementingClasses[] = $fullClassName;
                            }
                        }
                    }
                }
            }
        }

        return $implementingClasses;
    }
}