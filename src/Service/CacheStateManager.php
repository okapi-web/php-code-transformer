<?php

namespace Okapi\CodeTransformer\Service;

use Okapi\CodeTransformer\Service\Cache\CachePaths;
use Okapi\CodeTransformer\Service\Cache\CacheState;
use Okapi\Filesystem\Filesystem;
use Okapi\Singleton\Singleton;

/**
 * # Cache State Manager
 *
 * The `CacheStateManager` class is used to manage the cache state.
 *
 * 1. It creates the cache directory if it does not exist.
 * 2. It loads cached metadata into the {@link $cacheState} array.
 * 3. It adds new metadata (from new transformers) to the
 *    {@link $newCacheState} array.
 * 4. When the internal
 *    {@link https://www.php.net/manual/language.oop5.decon.php#language.oop5.decon.destructor __destruct()}
 *    method is called, it writes the new metadata to the cache file.
 */
class CacheStateManager implements ServiceInterface
{
    use Singleton;

    /**
     * Application directory keyword.
     */
    public const CODE_TRANSFORMER_APP_DIR = 'CODE_TRANSFORMER_APP_DIR';

    /**
     * Cache directory keyword.
     */
    public const CODE_TRANSFORMER_CACHE_DIR = 'CODE_TRANSFORMER_CACHE_DIR';

    /**
     * Cached metadata for the transformation state of a file.
     *
     * @var array<string, CacheState>
     */
    public array $cacheState = [];

    /**
     * Whether the cache state has changed.
     *
     * @var bool
     */
    private bool $cacheStateChanged = false;

    // region Initialization

    /**
     * Register the cache path manager.
     *
     * @return void
     */
    public static function register(): void
    {
        $instance = self::getInstance();
        $instance->ensureNotInitialized();

        $instance->initializeCacheDirectory();
        $instance->loadCacheState();

        $instance->setInitialized();
    }

    /**
     * Initialize the cache directory.
     *
     * @return void
     */
    private function initializeCacheDirectory(): void
    {
        Filesystem::mkdir(Options::$cacheDir, Options::$cacheFileMode);
    }

    /**
     * Load the saved cache state if it exists.
     *
     * @return void
     */
    private function loadCacheState(): void
    {
        $cacheFilePath = CachePaths::getCacheFilePath();

        if (!file_exists($cacheFilePath)) {
            return;
        }

        // Read file
        $cacheStateContent = Filesystem::readFile($cacheFilePath);

        // Replace the keywords
        $cacheStateContent = str_replace(
            [self::CODE_TRANSFORMER_APP_DIR, self::CODE_TRANSFORMER_CACHE_DIR],
            [addslashes(Options::$appDir), addslashes(Options::$cacheDir)],
            $cacheStateContent,
        );

        // Remove the opening PHP tag
        $cacheStateContent = preg_replace('/^<\?php/', '', $cacheStateContent);

        // Unserialize
        $cacheState = eval($cacheStateContent);

        // Convert to array of CacheState objects
        array_walk(
            $cacheState,
            function (&$cacheStateItem, $originalFilePath) {
                $cacheStateItem = new CacheState(
                    $originalFilePath,
                    $cacheStateItem['cachedFilePath'],
                    $cacheStateItem['transformedTime'],
                    $cacheStateItem['transformerFilePaths']
                );
            },
        );

        $this->cacheState = $cacheState;
    }

    // endregion

    // region Destructor

    /**
     * Automatic destructor saves the cache state.
     */
    public function __destruct()
    {
        $this->saveCacheState();
    }

    /**
     * Save the cache state.
     *
     * @return void
     */
    private function saveCacheState(): void
    {
        if (empty($this->cacheState)) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        // Opening PHP tag
        $phpCode = "<?php\n\nreturn ";

        // Serialize the cache state
        $phpCode .= var_export(
            array_map(
                fn (CacheState $cacheState) => $cacheState->toArray(),
                $this->cacheState,
            ),
            true,
        );

        // Semicolon
        $phpCode .= ';';

        // Replace the keywords
        $phpCode = str_replace(
            [addslashes(Options::$appDir), addslashes(Options::$cacheDir)],
            [self::CODE_TRANSFORMER_APP_DIR, self::CODE_TRANSFORMER_CACHE_DIR],
            $phpCode,
        );

        // Write file
        Filesystem::writeFile(
            CachePaths::getCacheFilePath(),
            $phpCode,
        );
    }

    // endregion

    /**
     * Query the cache state for a file.
     *
     * @param string $filePath
     *
     * @return ?CacheState
     */
    public static function queryCacheState(string $filePath): ?CacheState
    {
        $instance = self::getInstance();
        $instance->ensureInitialized();

        return $instance->cacheState[$filePath] ?? null;
    }

    /**
     * Set the cache state for a file.
     *
     * @param string     $filePath
     * @param CacheState $cacheState
     *
     * @return void
     */
    public static function setCacheState(
        string     $filePath,
        CacheState $cacheState,
    ): void {
        $instance = self::getInstance();
        $instance->ensureInitialized();

        $instance->cacheStateChanged = true;

        $instance->cacheState[$filePath] = $cacheState;
    }
}
