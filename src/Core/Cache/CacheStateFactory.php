<?php

namespace Okapi\CodeTransformer\Core\Cache;

use Okapi\CodeTransformer\Core\Cache\CacheState\EmptyResultCacheState;
use Okapi\CodeTransformer\Core\Cache\CacheState\NoTransformationsCacheState;
use Okapi\CodeTransformer\Core\Cache\CacheState\TransformedCacheState;
use Okapi\CodeTransformer\Core\DI;

/**
 * # Cache State Factory
 *
 * This class is used to create cache states from the cache state file.
 */
class CacheStateFactory
{
    /**
     * Map of cache state types to cache state classes.
     */
    public const CACHE_STATE_MAP = [
        'TransformedCacheState' => TransformedCacheState::class,
        'EmptyResultCacheState' => EmptyResultCacheState::class,
        'NoTransformationsCacheState' => NoTransformationsCacheState::class,
    ];

    /**
     * Create cache states from the cache state file.
     *
     * @param array<array-key, mixed> $cacheStatesArray
     *
     * @return array<string, CacheState>
     */
    public function createCacheStates(array $cacheStatesArray): array
    {
        $cacheStateObjects = [];

        foreach (array_keys($cacheStatesArray) as $cacheKey) {
            /** @var mixed $cacheStateArray */
            $cacheStateArray = $cacheStatesArray[$cacheKey];
            if (!is_array($cacheStateArray)) {
                continue;
            }

            /** @var mixed $type */
            $type = $cacheStateArray[CacheState::TYPE] ?? null;
            if (!is_string($type) || !isset(static::CACHE_STATE_MAP[$type])) {
                continue;
            }

            unset($cacheStateArray[CacheState::TYPE]);
            $cacheState = DI::make(static::CACHE_STATE_MAP[$type]);

            // Create if valid
            $cacheState = $cacheState->createIfValid($cacheStateArray);
            if (!$cacheState) {
                // @codeCoverageIgnoreStart
                continue;

                // @codeCoverageIgnoreEnd
            }

            // Add cache state to array
            $cacheStateObjects[$cacheState->originalFilePath] = $cacheState;
        }

        return $cacheStateObjects;
    }
}
