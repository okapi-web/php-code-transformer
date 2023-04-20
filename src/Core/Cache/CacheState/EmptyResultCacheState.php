<?php

namespace Okapi\CodeTransformer\Core\Cache\CacheState;

use Okapi\CodeTransformer\Core\Cache\CacheState;

/**
 * # Empty Result Cache State
 *
 * This class is used to represent an empty result cache state, which means that
 * the class was not matched by any transformer.
 */
class EmptyResultCacheState extends CacheState
{
    /**
     * @inheritDoc
     */
    public function getFilePath(): ?string
    {
        return null;
    }
}
