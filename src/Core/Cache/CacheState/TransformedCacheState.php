<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Cache\CacheState;

use Okapi\CodeTransformer\Core\Cache\CacheState;

/**
 * # Transformed Cache State
 *
 * This class is used to store the cache state for transformed files.
 */
class TransformedCacheState extends CacheState
{
    public const TRANSFORMED_FILE_PATH_KEY = 'transformedFilePath';
    public const TRANSFORMER_FILE_PATHS_KEY = 'transformerFilePaths';

    public string $transformedFilePath;
    public array $transformerFilePaths;

    /**
     * @inheritDoc
     */
    public function getRequiredKeys(): array
    {
        return array_merge(
            parent::getRequiredKeys(),
            [
                static::TRANSFORMED_FILE_PATH_KEY,
                static::TRANSFORMER_FILE_PATHS_KEY,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function isFresh(): bool
    {
        if (!parent::isFresh()) {
            return false;
        }

        // Check if the transformed file has been deleted
        if (!file_exists($this->transformedFilePath)) {
            return false;
        }

        // Check if any of the transformer files have been modified or deleted
        foreach ($this->transformerFilePaths as $transformerFilePath) {
            if (!file_exists($transformerFilePath)) {
                // @codeCoverageIgnoreStart
                return false;
                // @codeCoverageIgnoreEnd
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getFilePath(): ?string
    {
        return $this->transformedFilePath;
    }
}
