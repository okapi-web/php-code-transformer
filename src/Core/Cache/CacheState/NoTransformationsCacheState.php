<?php

namespace Okapi\CodeTransformer\Core\Cache\CacheState;

use Okapi\CodeTransformer\Core\Cache\CacheState;

/**
 * # No Transformations Cache State
 *
 * This class is used to represent a no transformations cache state, which
 * means that the file was matched by the transformers, but no transformations
 * were applied.
 */
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
