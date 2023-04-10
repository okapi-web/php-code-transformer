<?php

namespace Okapi\CodeTransformer\Service\ClassLoader;

class ClassContainer
{
    /**
     * The class paths.
     *
     * @var array<string, string>
     */
    private array $namespacedClassPaths = [];

    /**
     * Add a class path.
     *
     * @param string $path
     * @param string $class
     *
     * @return void
     */
    public function addNamespacedClassPath(string $path, string $class): void
    {
        $this->namespacedClassPaths[$path] = $class;
    }

    /**
     * Get a class path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getNamespacedClassByPath(string $path): string
    {
        return $this->namespacedClassPaths[$path];
    }
}
