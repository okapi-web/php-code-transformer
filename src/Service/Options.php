<?php

namespace Okapi\CodeTransformer\Service;

use Okapi\CodeTransformer\Service\Cache\CachePaths;
use Okapi\Path\Path;
use Okapi\Singleton\Singleton;

/**
 * # Options
 *
 * The `Options` class provides access to the options passed to the `CodeTransformerKernel`.
 */
class Options implements ServiceInterface
{
    use Singleton;

    /**
     * The application directory.
     *
     * @var string
     */
    public static string $appDir;

    /**
     * The cache directory.
     *
     * @var string
     */
    public static string $cacheDir;

    /**
     * The cache file mode.
     *
     * @var int
     */
    public static int $cacheFileMode;

    /**
     * Enable debug mode. This will disable the cache.
     *
     * @var bool
     */
    public static bool $debug;

    // region Pre-Initialization

    /**
     * Set the options.
     *
     * @param string|null $cacheDir
     * @param int|null    $cacheFileMode
     * @param bool|null   $debug
     */
    public static function setOptions(
        ?string $cacheDir,
        ?int    $cacheFileMode,
        ?bool   $debug,
    ): void {
        $rootDir = getcwd();

        if ($rootDir === false) {
            // @codeCoverageIgnoreStart
            $rootDir = Path::resolve(Path::join(__DIR__, '../../../../..'));
            // @codeCoverageIgnoreEnd
        }

        self::$appDir        = $rootDir;
        self::$cacheDir      = $cacheDir ?? Path::join($rootDir, CachePaths::DEFAULT_CACHE_DIR);
        self::$cacheFileMode = $cacheFileMode ?? (0777 & ~umask());
        self::$debug         = $debug ?? false;
    }

    // endregion

    // region Initialization

    /**
     * Register the options.
     *
     * @return void
     */
    public static function register(): void
    {
        $instance = self::getInstance();
        $instance->ensureNotInitialized();

        $instance->setInitialized();
    }

    // endregion
}
