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
    public const DATA = 'data';

    public const ORIGINAL_FILE_PATH_KEY = 'originalFilePath';
    public const NAMESPACED_CLASS_KEY   = 'namespacedClass';
    public const MODIFICATION_TIME_KEY  = 'modificationTime';

    public string $originalFilePath;
    protected string $namespacedClass;
    protected int $modificationTime;

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
            static::NAMESPACED_CLASS_KEY,
            static::MODIFICATION_TIME_KEY,
        ];
    }

    /**
     * Create a cache state if it is valid.
     *
     * @param array $cacheStateArray
     *
     * @return self|null
     */
    public function createIfValid(array $cacheStateArray): ?CacheState
    {
        if (!$this->valid($cacheStateArray)) {
            // @codeCoverageIgnoreStart
            return null;
            // @codeCoverageIgnoreEnd
        }

        $this->setData($cacheStateArray);

        return $this;
    }

    /**
     * Validate the cache state.
     *
     * @param array<string, (string|int|string[])> $cacheStateArray
     *
     * @return bool
     */
    private function valid(array $cacheStateArray): bool
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
    private function setData(array $cacheStateArray): void
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
        if (!file_exists($this->originalFilePath)) {
            // @codeCoverageIgnoreStart
            return false;
            // @codeCoverageIgnoreEnd
        }

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
}
