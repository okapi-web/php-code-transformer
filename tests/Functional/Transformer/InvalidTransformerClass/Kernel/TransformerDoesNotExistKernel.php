<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Util;

class TransformerDoesNotExistKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        'IDoNotExist',
    ];
}
