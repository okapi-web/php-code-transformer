<?php

namespace Okapi\CodeTransformer\Service\Cache;

use Okapi\CodeTransformer\Service\TransformerContainer;

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
     * @param string      $className
     * @param string|null $cachedFilePath
     * @param int         $transformedTime
     * @param string[]    $transformerFilePaths
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
        if ($this->cachedFilePath !== null) {
            // Prevent infinite recursion
            if ($this->originalFilePath === $this->cachedFilePath) {
                // @codeCoverageIgnoreStart
                // This should only happen if the project is misconfigured
                return false;
                // @codeCoverageIgnoreEnd
            }
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

        if ($this->cachedFilePath !== null) {
            $allFiles[] = $this->cachedFilePath;
        }

        // Check if the cache file exists
        foreach ($allFiles as $file) {
            if (!file_exists($file)) {
                return false;
            }
        }

        // Check if the transformer count is the same
        // Checking the count alone should be enough
        $cachedTransformerCount = count($this->transformerFilePaths);
        $currentTransformerCount = count(TransformerContainer::matchTransformers($this->className));
        if ($cachedTransformerCount !== $currentTransformerCount) {
            return false;
        }

        return true;
    }
}
