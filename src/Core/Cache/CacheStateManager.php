<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Cache;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Core\Container\TransformerManager;
use Okapi\CodeTransformer\Core\Options;
use Okapi\CodeTransformer\Core\ServiceInterface;
use Okapi\Filesystem\Filesystem;

/**
 * # Cache State Manager
 *
 * This class is used to manage the cache state.
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
    // region DI

    #[Inject]
    private Options $options;

    #[Inject]
    private CachePaths $cachePaths;

    #[Inject]
    private CacheStateFactory $cacheStateFactory;

    #[Inject]
    private TransformerManager $transformerManager;

    // endregion

    /**
     * Application directory keyword.
     */
    public const CODE_TRANSFORMER_APP_DIR = 'CODE_TRANSFORMER_APP_DIR';

    /**
     * Cache directory keyword.
     */
    public const CODE_TRANSFORMER_CACHE_DIR = 'CODE_TRANSFORMER_CACHE_DIR';

    /**
     * The hash keyword.
     */
    public const HASH = 'hash';

    /**
     * Cached metadata for the transformation state of a file.
     *
     * @var array<CacheState>
     */
    private array $cacheState = [];

    /**
     * New metadata for the transformation state of a file.
     *
     * @var array<CacheState>
     */
    private array $newCacheState = [];

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
            recursive: true,
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
        $cacheFileContent = Filesystem::readFile($cacheFilePath);

        $appDir   = $this->options->getAppDir();
        $cacheDir = $this->options->getCacheDir();

        // Replace the keywords
        $cacheFileContent = str_replace(
            [static::CODE_TRANSFORMER_APP_DIR, static::CODE_TRANSFORMER_CACHE_DIR],
            [addslashes($appDir), addslashes($cacheDir)],
            $cacheFileContent,
        );

        // Remove the opening PHP tag
        $cacheFileContent = preg_replace('/^<\?php/', '', $cacheFileContent);

        // Unserialize
        $cacheStatesArray = eval($cacheFileContent);

        // Check the hash
        $hash = $this->getHash();
        if (!isset($cacheStatesArray[static::HASH])
            || $cacheStatesArray[static::HASH] !== $hash
        ) {
            return;
        }
        unset($cacheStatesArray[static::HASH]);

        // Create the cache state
        $cacheStates = $this->cacheStateFactory->createCacheStates($cacheStatesArray);

        $this->cacheState = $cacheStates;
    }

    /**
     * Get the hash of the transformers.
     *
     * @return string
     */
    protected function getHash(): string
    {
        $transformers = $this->transformerManager->getTransformers();
        return md5(serialize($transformers));
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
        if (empty($this->newCacheState)) {
            // @codeCoverageIgnoreStart
            return;
            // @codeCoverageIgnoreEnd
        }

        // Opening PHP tag
        $phpCode = "<?php\n\nreturn ";

        // Create the cache state array
        $cacheStateArray = array_map(
            fn (CacheState $cacheState) => $cacheState->toArray(),
            array_merge($this->newCacheState, $this->cacheState),
        );

        // Set the hash
        $cacheStateArray[static::HASH] = $this->getHash();

        // Serialize the cache state
        $phpCode .= var_export($cacheStateArray, true);

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
        $this->newCacheState[$filePath] = $cacheState;
    }
}
