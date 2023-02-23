<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\InvalidTransformer;

class TransformerDoesNotExtendTransformerKernel extends CodeTransformerKernel
{
    protected array $transformers = [
        InvalidTransformer::class,
    ];
}
