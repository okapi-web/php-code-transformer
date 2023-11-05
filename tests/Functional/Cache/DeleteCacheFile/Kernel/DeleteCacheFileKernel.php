<?php

namespace Okapi\CodeTransformer\Tests\Functional\Cache\DeleteCacheFile\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Cache\DeleteCacheFile\Transformer\DeleteCacheFileTransformer;
use Okapi\CodeTransformer\Tests\Util;

class DeleteCacheFileKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        DeleteCacheFileTransformer::class,
    ];
}
