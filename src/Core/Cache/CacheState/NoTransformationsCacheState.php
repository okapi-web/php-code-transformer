<?php

namespace Okapi\CodeTransformer\Core\Cache\CacheState;

use Okapi\CodeTransformer\Core\Cache\CacheState;

// TODO: docs
class NoTransformationsCacheState extends CacheState
{
    /**
     * @inheritDoc
     */
    public function getFilePath(): ?string
    {
        return null;
    }
}
