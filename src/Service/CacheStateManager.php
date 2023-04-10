<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Service;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Service\Cache\CachePaths;
use Okapi\CodeTransformer\Service\Cache\CacheState;
use Okapi\Filesystem\Filesystem;

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
     *
     * @todo Implement this
     */
    private bool $cacheStateChanged = false;

    // region DI

    #[Inject]
    private Options $options;

    #[Inject]
    private CachePaths $cachePaths;

    // endregion

    // region Initialization

    /**
     * Register the cache path manager.
     *
     * @return void
     */
    public function register(): void
    {
        $this->initializeCacheDirectory();
        $this->loadCacheState();
    }

    /**
     * Initialize the cache directory.
     *
     * @return void
     */
    private function initializeCacheDirectory(): void
    {
        Filesystem::mkdir(
            $this->options->getCacheDir(),
            $this->options->getCacheFileMode(),
        );
    }

    /**
     * Load the saved cache state if it exists.
     *
     * @return void
     */
    private function loadCacheState(): void
    {
        $cacheFilePath = $this->cachePaths->getCacheFilePath();

        if (!file_exists($cacheFilePath)) {
            return;
        }

        // Read file
        $cacheStateContent = Filesystem::readFile($cacheFilePath);

        $appDir   = $this->options->getAppDir();
        $cacheDir = $this->options->getCacheDir();

        // Replace the keywords
        $cacheStateContent = str_replace(
            [static::CODE_TRANSFORMER_APP_DIR, static::CODE_TRANSFORMER_CACHE_DIR],
            [addslashes($appDir), addslashes($cacheDir)],
            $cacheStateContent,
        );

        // Remove the opening PHP tag
        $cacheStateContent = preg_replace('/^<\?php/', '', $cacheStateContent);

        // Unserialize
        $cacheState = eval($cacheStateContent);

        // Filter out invalid items
        $cacheState = array_filter(
            $cacheState,
            function ($cacheStateItem) {
                return key_exists('className', $cacheStateItem)
                    && key_exists('cachedFilePath', $cacheStateItem)
                    && key_exists('transformedTime', $cacheStateItem)
                    && key_exists('transformerFilePaths', $cacheStateItem);
            },
        );

        // Convert to array of CacheState objects
        array_walk(
            $cacheState,
            function (&$cacheStateItem, $originalFilePath) {
                $cacheStateItem = DI::make(CacheState::class, [
                    'originalFilePath'     => $originalFilePath,
                    'className'            => $cacheStateItem['className'],
                    'cachedFilePath'       => $cacheStateItem['cachedFilePath'],
                    'transformedTime'      => $cacheStateItem['transformedTime'],
                    'transformerFilePaths' => $cacheStateItem['transformerFilePaths'],
                ]);
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

        $appDir   = $this->options->getAppDir();
        $cacheDir = $this->options->getCacheDir();

        // Replace the keywords
        $phpCode = str_replace(
            [addslashes($appDir), addslashes($cacheDir)],
            [static::CODE_TRANSFORMER_APP_DIR, static::CODE_TRANSFORMER_CACHE_DIR],
            $phpCode,
        );

        // Write file
        Filesystem::writeFile(
            $this->cachePaths->getCacheFilePath(),
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
    public function queryCacheState(string $filePath): ?CacheState
    {
        return $this->cacheState[$filePath] ?? null;
    }

    /**
     * Set the cache state for a file.
     *
     * @param string     $filePath
     * @param CacheState $cacheState
     *
     * @return void
     */
    public function setCacheState(
        string     $filePath,
        CacheState $cacheState,
    ): void {
        $this->cacheStateChanged = true;

        $this->cacheState[$filePath] = $cacheState;
    }
}
