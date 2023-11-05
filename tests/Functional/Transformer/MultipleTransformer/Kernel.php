<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Transformer\UnPrivateTransformer;
use Okapi\CodeTransformer\Tests\Util;

class Kernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        StringTransformer::class,
        UnPrivateTransformer::class,
    ];
}
