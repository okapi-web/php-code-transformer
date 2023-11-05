<?php

namespace Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;

class CachedFileAndDestructorKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        StringTransformer::class,
    ];
}
