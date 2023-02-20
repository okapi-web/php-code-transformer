<?php

namespace Okapi\CodeTransformer;

use Okapi\CodeTransformer\Exception\Kernel\DirectKernelInitializationException;
use Okapi\Singleton\Singleton;

/**
 * # Code Transformer Kernel
 *
 * The `CodeTransformerKernel` is the heart of the Code Transformer library.
 * It manages an environment for Code Transformation.
 *
 * 1. Extends this class and define a list of transformers in the
 *    `$transformers` property.
 * 2. Call the `init()` method early in the application lifecycle.
 */
abstract class CodeTransformerKernel
{
    use Singleton;

    /**
     * List of transformers to be applied.
     *
     * @var class-string<Transformer>[]
     */
    protected array $transformers = [];

    /**
     * Initialize the kernel.
     *
     * @param string|null $cacheDir      The cache directory.
     *                                   <br><b>Default:</b> ROOT_DIR/cache/code-transformer<br>
     * @param int|null    $cacheFileMode The cache file mode.
     *                                   <br><b>Default:</b> 0777 & ~{@link umask()}<br>
     * @param bool|null   $debug         Enable debug mode. This will disable the cache.
     *                                   <br><b>Default:</b> false<br>
     *
     * @return void
     */
    public static function init(
        ?string $cacheDir,
        ?int    $cacheFileMode = null,
        bool    $debug = false,
    ): void {
        self::ensureNotKernelNamespace();

        $instance = self::getInstance();
        $instance->ensureNotAlreadyInitialized();

        if ($instance->transformers) {
            // TODO: Register services
        }

        $instance->setInitialized();
    }

    /**
     * Make sure that the kernel is not called from this class.
     *
     * @return void
     */
    private static function ensureNotKernelNamespace(): void
    {
        // Get current namespace and class name
        $namespace = get_called_class();

        if ($namespace === CodeTransformerKernel::class) {
            throw new DirectKernelInitializationException;
        }
    }
}
