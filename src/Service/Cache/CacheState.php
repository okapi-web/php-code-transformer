<?php

namespace Okapi\CodeTransformer\Service\Cache;

/**
 * # Cache State
 *
 * The `CacheState` class is responsible for storing the state of the cache.
 */
class CacheState
{
    /**
     * CacheState constructor.
     *
     * @param string      $originalFilePath
     * @param string|null $cachedFilePath
     * @param int         $transformedTime
     * @param string[]    $transformerFilePaths
     */
    public function __construct(
        public string  $originalFilePath,
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
        // Prevent infinite recursion
        if ($this->originalFilePath === $this->cachedFilePath) {
            // @codeCoverageIgnoreStart
            // This should only happen if the project is misconfigured
            return false;
            // @codeCoverageIgnoreEnd
        }

        $allFiles = array_merge(
            [$this->originalFilePath],
            $this->transformerFilePaths,
        );

        // Check if the files have been modified
        $lastModified = max(array_map('filemtime', $allFiles));
        if ($lastModified >= $this->transformedTime) {
            return false;
        }

        // Check if the cache file exists
        foreach (array_merge($allFiles, [$this->cachedFilePath]) as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }

        return true;
    }
}
