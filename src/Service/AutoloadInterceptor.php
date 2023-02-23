<?php

namespace Okapi\CodeTransformer\Service;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Okapi\CodeTransformer\Service\AutoloadInterceptor\ClassLoader;
use Okapi\CodeTransformer\Util\Finder;
use Okapi\Singleton\Singleton;

/**
 * # Autoload Interceptor
 *
 * The `AutoloadInterceptor` class is responsible for intercepting
 * the Composer Autoloader and applying the transformers.
 *
 * @see ClassLoader::__construct() - Initialization of the Code Transformer
 *                                   class loader.
 * @see ClassLoader::findFile() - Matching the class to the transformers and
 *                                replacing the original file with a PHP stream
 *                                filter.
 */
class AutoloadInterceptor implements ServiceInterface
{
    use Singleton;

    /**
     * Register the autoload interceptor.
     *
     * @return void
     */
    public static function register(): void
    {
        $instance = self::getInstance();
        $instance->ensureNotInitialized();

        // Overload existing composer loaders
        $instance->overloadComposerLoaders();

        $instance->setInitialized();
    }

    /**
     * Overload existing composer loaders
     *
     * @return void
     */
    private function overloadComposerLoaders(): void
    {
        $finder = $this->getFinder();

        // Get existing composer loaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            $loaderToUnregister = $loader;

            // Skip if not a class loader
            if (!is_array($loader)
                || !isset($loader[0])
                || !$loader[0] instanceof ComposerClassLoader
            ) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreEnd
            }

            // Register the AOP class loader
            $loader[0] = new ClassLoader($loader[0], $finder);

            // Unregister the original composer loader
            spl_autoload_unregister($loaderToUnregister);

            // Register the AOP class loader
            spl_autoload_register($loader);
        }
    }

    /**
     * Get class finder.
     *
     * @return Finder
     */
    private function getFinder(): Finder
    {
        return (new Finder)
            ->includeClass(TransformerContainer::getTargetClasses());
    }
}
