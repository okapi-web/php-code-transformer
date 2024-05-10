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
     * @var array<string, array{namespacedClass: class-string, cachedFilePath: string|null}>
     */
    private array $classContext = [];

    /**
     * Add a class path.
     *
     * @param string $path
     * @param class-string $namespacedClass
     * @param string|null $cachedFilePath
     *
     * @return void
     */
    public function addClassContext(
        string $path,
        string $namespacedClass,
        ?string $cachedFilePath = null,
    ): void {
        $this->classContext[$path] = [
            'namespacedClass' => $namespacedClass,
            'cachedFilePath' => $cachedFilePath,
        ];
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
        return $this->classContext[$path]['namespacedClass'];
    }

    public function getCachedFilePath(string $filePath): string
    {
        return $this->classContext[$filePath]['cachedFilePath'];
    }
}
