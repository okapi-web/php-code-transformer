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
     * @param $namespacedClass
     *
     * @return bool
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
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
     * Find the path to the file and match and apply the transformers.
     *
     * @param class-string $namespacedClass
     *
     * @return false|string
     *
     * @noinspection PhpParameterNameChangedDuringInheritanceInspection
     * @noinspection PhpStatementHasEmptyBodyInspection
     */
    public function findFile($namespacedClass): false|string
    {
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


        // Query cache state
        $cacheState = $this->cacheStateManager->queryCacheState($filePath);

        // When debugging, bypass the caching mechanism
        if ($this->options->isDebug()) {
            // ...
        }

        // In production mode, use the cache without checking if it is fresh
        elseif ($this->options->getEnvironment() === Environment::PRODUCTION
            && $cacheState
        ) {
            // Use the cached file if transformations have been applied
            // Or return the original file if no transformations have been applied
            return $cacheState->getFilePath() ?? $filePath;
        }

        // In development mode, check if the cache is fresh
        elseif ($this->options->getEnvironment() === Environment::DEVELOPMENT
            && $cacheState
            && $cacheState->isFresh()
        ) {
            return $cacheState->getFilePath() ?? $filePath;
        }


        // Check if the class should be transformed
        if (!$this->transformerMatcher->match($namespacedClass, $filePath)) {
            return $filePath;
        }

        // Add the class to store the file path
        $this->classContainer->addNamespacedClassPath($filePath, $namespacedClass);

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
