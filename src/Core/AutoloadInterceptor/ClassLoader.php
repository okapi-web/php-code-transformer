<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\AutoloadInterceptor;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use DI\Attribute\Inject;
use Okapi\CodeTransformer\Core\AutoloadInterceptor;
use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
use Okapi\CodeTransformer\Core\Matcher\TransformerMatcher;
use Okapi\CodeTransformer\Core\Options;
use Okapi\CodeTransformer\Core\Options\Environment;
use Okapi\CodeTransformer\Core\StreamFilter;
use Okapi\CodeTransformer\Core\StreamFilter\FilterInjector;
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
    protected TransformerMatcher $transformerMatcher;

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
     * @param ComposerClassLoader $originalClassLoader
     *
     * @noinspection PhpMissingParentConstructorInspection (Parent already constructed)
     */
    public function __construct(
        public ComposerClassLoader $originalClassLoader,
    ) {}

    /**
     * Autoload a class.
     *
     * @param string $class
     *
     * @return true|null
     */
    public function loadClass($class): ?bool
    {
        $file = $this->findFile($class);
        if ($file !== false) {
            include $file;
            return true;
        }

        return null;
    }

    /**
     * Find the path to the file and match and apply the transformers.
     *
     * @param string $class
     *
     * @return false|string
     */
    public function findFile($class): false|string
    {
        /** @var class-string $namespacedClass */
        $namespacedClass = $class;
        $filePath = $this->originalClassLoader->findFile($namespacedClass);

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
        if (!is_string($filePath)) {
            return false;
        }

        foreach ($this->options->getExcludePaths() as $path) {
            $resolvedPath = Path::resolve($path);
            if (is_string($resolvedPath) && str_starts_with($filePath, $resolvedPath)) {
                return $filePath;
            }
        }

        // Query cache state
        $cacheState = $this->cacheStateManager->queryCacheState($filePath);

        $useCache =
            !$this->options->isDebug()
            && $cacheState !== null
            && ($this->options->getEnvironment() === Environment::PRODUCTION || $cacheState->isFresh());
        if ($useCache) {
            $cacheFilePath = $cacheState->getFilePath();
            if ($cacheFilePath !== null) {
                $this->classContainer->addClassContext($filePath, $namespacedClass, $cacheFilePath);
                return $this->filterInjector->rewriteCached($filePath);
            }

            return $filePath;
        }

        // Check if the class should be transformed
        if (!$this->transformerMatcher->matchAndStore($namespacedClass, $filePath)) {
            return $filePath;
        }

        // Add the class to store the file path
        $this->classContainer->addClassContext($filePath, $namespacedClass);

        // Replace the file path with a PHP stream filter
        /** @see StreamFilter::filter() */
        return $this->filterInjector->rewrite($filePath);
    }

    /**
     * Check if the class is internal to the Code Transformer.
     *
     * @param string $namespacedClass
     *
     * @return bool
     */
    protected function isInternal(string $namespacedClass): bool
    {
        return str_starts_with_any_but_not(
            $namespacedClass,
            [
                'Okapi\\CodeTransformer\\',
                'Okapi\\Path\\',
                'Okapi\\Wildcards\\',
                'DI\\',
            ],
            [
                'Okapi\\CodeTransformer\\Tests\\',
            ],
        );
    }
}
