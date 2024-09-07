<?php

namespace Okapi\CodeTransformer\Core;

use Composer\Autoload\ClassLoader;
use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Core\Options\Environment;
use Okapi\Path\Path;
use ReflectionClass;

/**
 * # Options
 *
 * This class provides access to the options passed to the
 * {@see CodeTransformerKernel}.
 */
class Options implements ServiceInterface
{
    // region Options

    /**
     * The application directory.
     *
     * @var string
     */
    private string $appDir;

    /**
     * The cache directory.
     *
     * @var string
     */
    private string $cacheDir;

    /**
     * The exclude paths.
     *
     * @var array
     */
    private array $excludePaths;

    /**
     * The cache file mode.
     *
     * @var int
     */
    private int $cacheFileMode;

    /**
     * Enable debug mode. This will disable the cache.
     *
     * @var bool
     */
    private bool $debug;

    /**
     * The environment in which the application is running.
     *
     * @var Environment
     */
    private Environment $environment;

    // endregion

    /**
     * # Default cache directory.
     *
     * This directory is used if no cache directory is provided.
     *
     * @var string
     */
    public string $defaultCacheDir = 'cache/code-transformer';

    // region Pre-Initialization

    /**
     * Set the options.
     *
     * @param string|null $cacheDir
     * @param int|null    $cacheFileMode
     * @param bool        $debug
     * @param Environment $environment
     */
    public function setOptions(
        ?string     $cacheDir,
        ?int        $cacheFileMode,
        bool        $debug,
        Environment $environment,
        array       $excludePaths = [],
    ): void {
        $composerRef = new ReflectionClass(ClassLoader::class);
        $composerDir = $composerRef->getFileName();
        $rootDir     = Path::resolve(Path::join($composerDir, '../../..'));

        $this->appDir        = $rootDir;
        $this->cacheDir      = $cacheDir ?? Path::join($rootDir, $this->defaultCacheDir);
        $this->cacheFileMode = $cacheFileMode ?? (0777 & ~umask());
        $this->debug         = $debug;
        $this->environment   = $environment;
        $this->excludePaths  = $excludePaths;
    }

    // endregion

    // region Initialization

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        // Nothing to do here.
    }

    // endregion

    /**
     * Get the application directory.
     */
    public function getAppDir(): string
    {
        return $this->appDir;
    }

    /**
     * Get the cache directory.
     */
    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * Get the exclude paths.
     */
    public function getExcludePaths(): array
    {
        return $this->excludePaths;
    }

    /**
     * Get the cache file mode.
     */
    public function getCacheFileMode(): int
    {
        return $this->cacheFileMode;
    }

    /**
     * Check if debug mode is enabled.
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * Get the environment in which the application is running.
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}
