<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\ChangedClass\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Target\ChangedClass\Transformer\ChangedClassTransformer;
use Okapi\CodeTransformer\Tests\Util;

class ChangedClassKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        ChangedClassTransformer::class,
    ];
}
