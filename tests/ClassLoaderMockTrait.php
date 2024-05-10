<?php

namespace Okapi\CodeTransformer\Tests;

use Okapi\CodeTransformer\Core\AutoloadInterceptor\ClassLoader;
use Okapi\CodeTransformer\Core\CachedStreamFilter;
use Okapi\CodeTransformer\Core\StreamFilter;
use Okapi\CodeTransformer\Core\StreamFilter\FilterInjector;
use Okapi\Path\Path;
use PHPUnit\Framework\Assert;
use ReflectionProperty;

trait ClassLoaderMockTrait
{
    private ?ClassLoader $classLoader = null;

    private function findClassMock(string $class): string
    {
        if (!isset($this->classLoader)) {
            $this->findClassLoader();
        }

        return $this->classLoader->findFile($class);
    }

    private function findOriginalClassMock(string $class): string
    {
        if (!isset($this->classLoader)) {
            $this->findClassLoader();
        }

        $original = new ReflectionProperty(ClassLoader::class, 'originalClassLoader');
        $original = $original->getValue($this->classLoader);
        return $original->findFile($class);
    }

    private function findClassLoader(): void
    {
        foreach (spl_autoload_functions() as $function) {
            if (is_array($function) && $function[0] instanceof ClassLoader) {
                $this->classLoader = $function[0];
                break;
            }
        }
    }

    public function assertWillBeTransformed(string $className): void
    {
        $originalFilePath = Path::resolve($this->findOriginalClassMock($className));

        $transformPath =
            FilterInjector::PHP_FILTER_READ .
            StreamFilter::FILTER_ID . '/resource=' .
            $originalFilePath;

        $filePathMock = $this->findClassMock($className);

        Assert::assertEquals(
            $transformPath,
            $filePathMock,
            "$className will not be transformed",
        );
    }

    public function assertTransformerLoadedFromCache(string $className): void
    {
        $filePath = Path::resolve($this->findOriginalClassMock($className));

        $cachePath =
            FilterInjector::PHP_FILTER_READ .
            CachedStreamFilter::CACHED_FILTER_ID . '/resource=' .
            $filePath;

        $filePathMock = $this->findClassMock($className);

        Assert::assertEquals(
            $cachePath,
            $filePathMock,
            $className . ' will not be loaded from cache',
        );
    }

    public function assertTransformerNotApplied(string $className): void
    {
        $originalFilePath = Path::resolve($this->findOriginalClassMock($className));
        $filePathMock = $this->findClassMock($className);

        Assert::assertEquals(
            $originalFilePath,
            $filePathMock,
            $className . ' will be transformed',
        );
    }
}
