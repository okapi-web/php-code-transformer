<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Transformer\UnPrivateTransformer;
use Okapi\CodeTransformer\Tests\Util;

class MultipleTransformerKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        StringTransformer::class,
        UnPrivateTransformer::class,
    ];
}
