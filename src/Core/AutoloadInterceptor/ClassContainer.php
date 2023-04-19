<?php

namespace Okapi\CodeTransformer\Core\AutoloadInterceptor;

/**
 * # Class Container
 *
 * This class is used to store the class paths between the Code Transformer
 * class loader and the PHP stream filter.
 */
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
