<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use DI\Attribute\Inject;
use Okapi\CodeTransformer\Core\AutoloadInterceptor\ClassLoader;
use Okapi\CodeTransformer\Core\Util\ReflectionHelper;

/**
 * # Autoload Interceptor
 *
 * This class is responsible for intercepting the Composer Autoloader and
 * applying the transformers.
 *
 * @see ClassLoader::__construct() - Initialization of the Code Transformer
 *                                   class loader.
 * @see ClassLoader::findFile() - Matching the class to the transformers and
 *                                replacing the original file with a PHP stream
 *                                filter.
 */
class AutoloadInterceptor implements ServiceInterface
{
    // region DI

    #[Inject]
    private ReflectionHelper $reflectionHelper;

    // endregion

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
            $originalClassLoader = $loader[0];
            $this->reflectionHelper->setClassLoader($originalClassLoader);

            // Create the Code Transformer class loader
            $loader[0] = DI::make(ClassLoader::class, [
                'originalClassLoader' => $originalClassLoader,
            ]);

            // Unregister the original composer loader
            spl_autoload_unregister($loaderToUnregister);

            // Register the Code Transformer class loader
            spl_autoload_register($loader);
        }
    }
}
