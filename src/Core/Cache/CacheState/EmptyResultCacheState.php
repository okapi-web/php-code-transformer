<?php

namespace Okapi\CodeTransformer\Core\Cache\CacheState;

use Okapi\CodeTransformer\Core\Cache\CacheState;

/**
 * TODO: docs
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
