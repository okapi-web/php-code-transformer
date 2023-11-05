<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Transformer\AddedTransformer1;
use Okapi\CodeTransformer\Tests\Util;

class BeforeAddedTransformerKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        AddedTransformer1::class,
    ];
}
