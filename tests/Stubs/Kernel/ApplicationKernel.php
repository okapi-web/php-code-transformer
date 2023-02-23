<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\ChangedTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\DeleteCacheFileTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\NoChangesTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\SyntaxErrorTransformer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\UnPrivateTransformer;

class ApplicationKernel extends CodeTransformerKernel
{
    protected array $transformers = [
        StringTransformer::class,
        NoChangesTransformer::class,
        SyntaxErrorTransformer::class,
        ChangedTransformer::class,
        DeleteCacheFileTransformer::class,
        UnPrivateTransformer::class,
    ];
}
