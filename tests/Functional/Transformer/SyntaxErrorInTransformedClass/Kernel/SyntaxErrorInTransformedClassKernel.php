<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass\Transformer\SyntaxErrorTransformer;
use Okapi\CodeTransformer\Tests\Util;

class SyntaxErrorInTransformedClassKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        SyntaxErrorTransformer::class,
    ];
}
