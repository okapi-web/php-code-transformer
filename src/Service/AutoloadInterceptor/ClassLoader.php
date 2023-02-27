<?php

namespace Okapi\CodeTransformer\Service\AutoloadInterceptor;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Okapi\CodeTransformer\Service\AutoloadInterceptor;
use Okapi\CodeTransformer\Service\CacheStateManager;
use Okapi\CodeTransformer\Service\Options;
use Okapi\CodeTransformer\Service\StreamFilter;
use Okapi\CodeTransformer\Service\StreamFilter\FilterInjector;
use Okapi\CodeTransformer\Service\TransformerContainer;
use Okapi\Path\Path;
use Okapi\Wildcards\Regex;

/**
 * # Code Transformer Class Loader
 *
 * This class loader is responsible for loading classes that should be
 * intercepted by the Code Transformer.
 *
 * @see AutoloadInterceptor::overloadComposerLoaders() - Initialization of the Code Transformer class loader.
 * @see FilterInjector::rewrite() - Switching the original file with a PHP filter.
 * @see StreamFilter::filter() - Applying the transformations to the file.
 */
class ClassLoader extends ComposerClassLoader
{
    /**
     * Code Transformer class loader constructor.
     *
     * @param ComposerClassLoader $original
     *
     * @noinspection PhpMissingParentConstructorInspection (Parent already constructed)
     */
    public function __construct(
        private readonly ComposerClassLoader $original,
    ) {}

    /**
     * Autoload a class.
     *
     * @param $class
     *
     * @return bool
     */
    public function loadClass($class): bool
    {
        if ($file = $this->findFile($class)) {
            include $file;

            return true;
        }

        // @codeCoverageIgnoreStart
        // Not sure how to test this
        return false;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Find the path to the file and apply the transformers.
     *
     * @param $class
     *
     * @return false|string
     */
    public function findFile($class): false|string
    {
        $filePath = $this->original->findFile($class);

        // Prevent infinite recursion
        if ($class === Regex::class) {
            return $filePath;
        }

        // @codeCoverageIgnoreStart
        // Not sure how to test this
        if ($filePath === false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        $filePath = Path::resolve($filePath);

        // Check if the class should be transformed
        if (TransformerContainer::shouldTransform($class)) {
            $cacheState = CacheStateManager::queryCacheState($filePath);

            // Check if the file is cached and up to date
            if (!Options::$debug && $cacheState?->isFresh()) {
                // Use the cached file if transformations have been applied
                // Or return the original file if no transformations have been applied
                return $cacheState->cachedFilePath ?? $filePath;
            }

            // Replace the file path with a PHP stream filter
            /** @see StreamFilter::filter() */
            return FilterInjector::rewrite($filePath);
        }

        return $filePath;
    }
}
