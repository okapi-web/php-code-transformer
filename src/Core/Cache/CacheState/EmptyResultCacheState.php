<?php

namespace Okapi\CodeTransformer\Core\Cache\CacheState;

use Okapi\CodeTransformer\Core\Cache\CacheState;

/**
 * # Empty Result Cache State
 *
 * This class is used to represent an empty result cache state, which means that
 * the class was not matched by any transformer.
 *
 * @todo: I think when a transformer is changed, the cache state should be
 *   invalidated. This is not currently the case. Maybe clear the whole cache
 *   when a transformer is changed?
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
