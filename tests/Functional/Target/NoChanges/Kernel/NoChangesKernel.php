<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\NoChanges\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Target\NoChanges\Transformer\NoChangesTransformer;
use Okapi\CodeTransformer\Tests\Util;

class NoChangesKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        NoChangesTransformer::class,
    ];
}
