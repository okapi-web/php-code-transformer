<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Service\ClassLoader;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use DI\Attribute\Inject;
use Okapi\CodeTransformer\Service\AutoloadInterceptor;
use Okapi\CodeTransformer\Service\CacheStateManager;
use Okapi\CodeTransformer\Service\Matcher\TransformerMatcher;
use Okapi\CodeTransformer\Service\Options;
use Okapi\CodeTransformer\Service\StreamFilter;
use Okapi\CodeTransformer\Service\StreamFilter\FilterInjector;
use Okapi\Path\Path;

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
    // region DI

    #[Inject]
    private TransformerMatcher $transformerMatcher;

    #[Inject]
    protected CacheStateManager $cacheStateManager;

    #[Inject]
    protected Options $options;

    #[Inject]
    protected FilterInjector $filterInjector;

    #[Inject]
    protected ClassContainer $classContainer;

    // endregion

    /**
     * Code Transformer class loader constructor.
     *
     * @param ComposerClassLoader $original
     *
     * @noinspection PhpMissingParentConstructorInspection (Parent already constructed)
     */
    public function __construct(
        public readonly ComposerClassLoader $original,
    ) {}

    /**
     * Autoload a class.
     *
     * @param $namespacedClass
     *
     * @return bool
     */
    public function loadClass($namespacedClass): bool
    {
        if ($file = $this->findFile($namespacedClass)) {
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
     * @param $namespacedClass
     *
     * @return false|string
     */
    public function findFile($namespacedClass): false|string
    {
        $filePath = $this->original->findFile($namespacedClass);

        // @codeCoverageIgnoreStart
        // Not sure how to test this
        if ($filePath === false) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        // Prevent infinite recursion
        if ($this->isInternal($namespacedClass)) {
            return $filePath;
        }

        $filePath = Path::resolve($filePath);

        // Check if the class should be transformed
        if ($this->transformerMatcher->shouldTransform($namespacedClass)) {
            $cacheState = $this->cacheStateManager->queryCacheState($filePath);

            // Check if the file is cached and up to date
            if (!$this->options->isDebug() && $cacheState?->isFresh()) {
                // Use the cached file if transformations have been applied
                // Or return the original file if no transformations have been applied
                return $cacheState->cachedFilePath ?? $filePath;
            }

            // Add the class to store the file path
            $this->classContainer->addNamespacedClassPath($filePath, $namespacedClass);

            // Replace the file path with a PHP stream filter
            /** @see StreamFilter::filter() */
            return $this->filterInjector->rewrite($filePath);
        }

        return $filePath;
    }

    /**
     * Check if the class is internal to the Code Transformer.
     *
     * @param string $class
     *
     * @return bool
     */
    protected function isInternal(string $class): bool
    {
        // Code Transformer
        if (str_starts_with($class, "Okapi\\CodeTransformer\\")
            && !str_starts_with($class, "Okapi\\CodeTransformer\\Tests\\")) {
            return true;
        }

        // Wildcards
        if (str_starts_with($class, "Okapi\\Wildcards\\")) {
            return true;
        }

        // DI
        if (str_starts_with($class, "DI\\")) {
            return true;
        }

        return false;
    }
}
