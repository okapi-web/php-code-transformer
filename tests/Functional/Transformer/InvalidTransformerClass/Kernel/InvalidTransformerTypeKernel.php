<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;

class InvalidTransformerTypeKernel extends CodeTransformerKernel
{
    protected array $transformers = [
        42,
    ];
}
