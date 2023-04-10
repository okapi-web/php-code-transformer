<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\AddedTransformer1;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\ChangedTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\DeleteCacheFileTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\NoChangesTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\SyntaxErrorTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\UnPrivateTransformer;
use Okapi\CodeTransformer\Tests\Util;

class ApplicationKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected array $transformers = [
        StringTransformer::class,
        NoChangesTransformer::class,
        SyntaxErrorTransformer::class,
        ChangedTransformer::class,
        DeleteCacheFileTransformer::class,
        UnPrivateTransformer::class,
        AddedTransformer1::class,
    ];
}
