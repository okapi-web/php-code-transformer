<?php

namespace Okapi\CodeTransformer\Service;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Okapi\CodeTransformer\Service\ClassLoader\ClassLoader;

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
    /**
     * The DI key for the original composer class loader.
     */
    public const DI = 'okapi.code-transformer.service.composer.class-loader';

    /**
     * Register the autoload interceptor.
     *
     * @return void
     */
    public function register(): void
    {
        // Overload existing composer loaders
        $this->overloadComposerLoaders();
    }

    /**
     * Overload existing composer loaders
     *
     * @return void
     */
    private function overloadComposerLoaders(): void
    {
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

            // Get the original composer loader
            $original = $loader[0];
            DI::set(self::DI, $original);

            // Register the AOP class loader
            $loader[0] = DI::make(ClassLoader::class, [
                'original' => $original,
            ]);

            // Unregister the original composer loader
            spl_autoload_unregister($loaderToUnregister);

            // Register the AOP class loader
            spl_autoload_register($loader);
        }
    }
}
