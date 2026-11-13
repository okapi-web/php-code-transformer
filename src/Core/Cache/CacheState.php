<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Cache;

use ReflectionProperty;

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
    public const NAMESPACED_CLASS_KEY = 'namespacedClass';
    public const MODIFICATION_TIME_KEY = 'modificationTime';

    public string $originalFilePath;
    protected string $namespacedClass;
    protected int $modificationTime;

    /**
     * CacheState constructor.
     *
     * @param array<array-key, mixed> $data
     */
    public function __construct(array $data = [])
    {
        $this->setData($data);
    }

    /**
     * Get the cache state as an array.
     *
     * @return array<array-key, mixed>
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
     * @return array<array-key, mixed>
     */
    private function getAllData(): array
    {
        $data = [];

        foreach ($this->getRequiredKeys() as $key) {
            $property = new ReflectionProperty($this, $key);
            /** @var mixed $value */
            $value = $property->getValue($this);
            $data[$key] = $value;
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
        $separator = strrpos(static::class, '\\');
        return $separator === false ? static::class : substr(static::class, $separator + 1);
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
     * @param array<array-key, mixed> $cacheStateArray
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
     * @param array<array-key, mixed> $cacheStateArray
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
     * @param array<array-key, mixed> $cacheStateArray
     */
    private function setData(array $cacheStateArray): void
    {
        foreach (array_keys($cacheStateArray) as $key) {
            /** @var mixed $value */
            $value = $cacheStateArray[$key];
            if (!is_string($key) || !property_exists($this, $key)) {
                continue;
            }
            (new ReflectionProperty($this, $key))->setValue($this, $value);
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

        $modified = filemtime($this->originalFilePath);
        if ($modified === false || $modified > $this->modificationTime) {
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
