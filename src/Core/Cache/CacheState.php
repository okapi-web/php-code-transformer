<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Cache;

/**
 * # Cache State
 *
 * This class is responsible for storing the state of the cache.
 */
abstract class CacheState
{
    public const TYPE = 'type';

    public const ORIGINAL_FILE_PATH_KEY = 'originalFilePath';
    public const MODIFICATION_TIME_KEY  = 'modificationTime';

    public string $originalFilePath;
    public int $modificationTime;

    /**
     * CacheState constructor.
     *
     * @param array<string, (string|int|string[])> $data
     */
    public function __construct(
        array $data = [],
    ) {
        $this->setData($data);
    }

    /**
     * Get the cache state as an array.
     *
     * @return array<string, (string|int|string[])>
     */
    public function toArray(): array
    {
        return [
            static::TYPE => $this->getType(),
            ...$this->getAllData(),
        ];
    }

    /**
     * Get the cache state as an array.
     *
     * @return array<string, (string|int|string[])>
     */
    private function getAllData(): array
    {
        $data = [];

        foreach ($this->getRequiredKeys() as $key) {
            $data[$key] = $this->{$key};
        }

        return $data;
    }

    /**
     * Get the cache state type.
     *
     * @return string
     */
    protected function getType(): string
    {
        // Return the class name without the namespace
        return substr(
            static::class,
            strrpos(static::class, '\\') + 1,
        );
    }

    /**
     * Get the required keys for the cache state.
     *
     * @return string[]
     */
    public function getRequiredKeys(): array
    {
        return [
            static::ORIGINAL_FILE_PATH_KEY,
            static::MODIFICATION_TIME_KEY,
        ];
    }

    /**
     * Validate the cache state.
     *
     * @param array<string, (string|int|string[])> $cacheStateArray
     *
     * @return bool
     */
    public function valid(array $cacheStateArray): bool
    {
        // Check if all required keys are present
        foreach ($this->getRequiredKeys() as $requiredKey) {
            if (!isset($cacheStateArray[$requiredKey])) {
                // @codeCoverageIgnoreStart
                return false;
                // @codeCoverageIgnoreEnd
            }
        }

        return true;
    }

    /**
     * Set the cache state data.
     *
     * @param array<string, (string|int|string[])> $cacheStateArray
     */
    public function setData(array $cacheStateArray): void
    {
        foreach ($cacheStateArray as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * Check if the cache state is fresh (not outdated).
     *
     * @return bool
     */
    public function isFresh(): bool
    {
        if (filemtime($this->originalFilePath) > $this->modificationTime) {
            return false;
        }

        return true;
    }

    /**
     * Get the file path.
     *
     * @return string|null
     */
    abstract public function getFilePath(): ?string;

    // /**
    //  * CacheState constructor.
    //  *
    //  * @param string        $originalFilePath
    //  * @param string        $className
    //  * @param string|null   $cachedFilePath
    //  * @param int|null      $transformedTime
    //  * @param string[]|null $transformerFilePaths
    //  */
    // public function __construct(
    //     public string  $originalFilePath,
    //     public string  $className,
    //     public ?string $cachedFilePath,
    //     public ?int    $transformedTime,
    //     public ?array  $transformerFilePaths,
    // ) {}
    //
    // /**
    //  * Use the cached file path if aspects have been applied.
    //  * Otherwise, use the original file path if no aspects have been applied.
    //  *
    //  * @return string
    //  */
    // public function getFilePath(): string
    // {
    //     return $this->cachedFilePath ?? $this->originalFilePath;
    // }
    //
    //
    //
    //
    // /**
    //  * Get the cache state as an array.
    //  *
    //  * @return array
    //  */
    // public function toArray(): array
    // {
    //     return [
    //         $this->originalFilePath,
    //         $this->className,
    //         $this->cachedFilePath,
    //         $this->transformedTime,
    //         $this->transformerFilePaths,
    //     ];
    // }
    //
    // /**
    //  * Check if the cache is not outdated.
    //  *
    //  * @return bool
    //  */
    // public function isFresh(): bool
    // {
    //     // @codeCoverageIgnoreStart
    //     // This should only happen if the project is misconfigured
    //     if ($this->checkInfiniteLoop()) {
    //         return false;
    //     }
    //     // @codeCoverageIgnoreEnd
    //
    //     $allFiles = array_merge(
    //         [$this->originalFilePath],
    //         $this->transformerFilePaths,
    //     );
    //
    //     if ($this->checkFilesModified($allFiles)) {
    //         return false;
    //     }
    //
    //     if ($this->cachedFilePath) {
    //         $allFiles[] = $this->cachedFilePath;
    //     }
    //
    //     if (!$this->checkFilesExist($allFiles)) {
    //         return false;
    //     }
    //
    //     if (!$this->checkTransformerCount()) {
    //         return false;
    //     }
    //
    //     return true;
    // }
    //
    // /**
    //  * Check if the cache is in an infinite loop.
    //  *
    //  * @return bool True if the cache is in an infinite loop
    //  */
    // protected function checkInfiniteLoop(): bool
    // {
    //     if ($this->cachedFilePath !== null) {
    //         // Same original file and cached file
    //         if ($this->originalFilePath === $this->cachedFilePath) {
    //             return true;
    //         }
    //     }
    //
    //     return false;
    // }
    //
    // /**
    //  * Check if the files have been modified.
    //  *
    //  * @param string[] $files
    //  *
    //  * @return bool True if any file has been modified
    //  */
    // protected function checkFilesModified(array $files): bool
    // {
    //     $lastModified = max(array_map('filemtime', $files));
    //     if ($lastModified >= $this->transformedTime) {
    //         return true;
    //     }
    //
    //     return false;
    // }
    //
    // /**
    //  * Check if the files exist.
    //  *
    //  * @param string[] $files
    //  *
    //  * @return bool True if all files exist
    //  */
    // protected function checkFilesExist(array $files): bool
    // {
    //     // Check if the cache file exists
    //     foreach ($files as $file) {
    //         if (!file_exists($file)) {
    //             return false;
    //         }
    //     }
    //
    //     return true;
    // }
    //
    // /**
    //  * Check if the transformer count is the same.
    //  *
    //  * @return bool True if the count is the same
    //  */
    // protected function checkTransformerCount(): bool
    // {
    //     // Checking the count alone should be enough
    //     $cachedTransformerCount  = count($this->transformerFilePaths);
    //     $currentTransformerCount = count(
    //         $this->transformerMatcher->match($this->className),
    //     );
    //     if ($cachedTransformerCount !== $currentTransformerCount) {
    //         return false;
    //     }
    //
    //     return true;
    // }
}
