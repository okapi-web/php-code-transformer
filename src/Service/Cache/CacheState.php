<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Service\Cache;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Service\Matcher\TransformerMatcher;

/**
 * # Cache State
 *
 * The `CacheState` class is responsible for storing the state of the cache.
 */
class CacheState
{
    // region DI

    #[Inject]
    protected TransformerMatcher $transformerMatcher;

    // endregion

    /**
     * CacheState constructor.
     *
     * @param string               $originalFilePath
     * @param string               $className
     * @param string|null          $cachedFilePath
     * @param int                  $transformedTime
     * @param string[]             $transformerFilePaths
     */
    public function __construct(
        public string  $originalFilePath,
        public string  $className,
        public ?string $cachedFilePath,
        public int     $transformedTime,
        public array   $transformerFilePaths,
    ) {}

    /**
     * Get the cache state as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'className'            => $this->className,
            'cachedFilePath'       => $this->cachedFilePath,
            'transformedTime'      => $this->transformedTime,
            'transformerFilePaths' => $this->transformerFilePaths,
        ];
    }

    /**
     * Check if the cache is not outdated.
     *
     * @return bool
     */
    public function isFresh(): bool
    {
        // @codeCoverageIgnoreStart
        // This should only happen if the project is misconfigured
        if ($this->checkInfiniteLoop()) {
            return false;
        }
        // @codeCoverageIgnoreEnd

        $allFiles = array_merge(
            [$this->originalFilePath],
            $this->transformerFilePaths,
        );

        if ($this->checkFilesModified($allFiles)) {
            return false;
        }

        if ($this->cachedFilePath) {
            $allFiles[] = $this->cachedFilePath;
        }

        if (!$this->checkFilesExist($allFiles)) {
            return false;
        }

        if (!$this->checkTransformerCount()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the cache is in an infinite loop.
     *
     * @return bool True if the cache is in an infinite loop
     */
    protected function checkInfiniteLoop(): bool
    {
        if ($this->cachedFilePath !== null) {
            // Same original file and cached file
            if ($this->originalFilePath === $this->cachedFilePath) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the files have been modified.
     *
     * @param string[] $files
     *
     * @return bool True if any file has been modified
     */
    protected function checkFilesModified(array $files): bool
    {
        $lastModified = max(array_map('filemtime', $files));
        if ($lastModified >= $this->transformedTime) {
            return true;
        }

        return false;
    }

    /**
     * Check if the files exist.
     *
     * @param string[] $files
     *
     * @return bool True if all files exist
     */
    protected function checkFilesExist(array $files): bool
    {
        // Check if the cache file exists
        foreach ($files as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the transformer count is the same.
     *
     * @return bool True if the count is the same
     */
    protected function checkTransformerCount(): bool
    {
        // Checking the count alone should be enough
        $cachedTransformerCount = count($this->transformerFilePaths);
        $currentTransformerCount = count(
            $this->transformerMatcher->match($this->className)
        );
        if ($cachedTransformerCount !== $currentTransformerCount) {
            return false;
        }

        return true;
    }
}
