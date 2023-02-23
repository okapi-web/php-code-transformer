<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;

class TransformerDoesNotExistKernel extends CodeTransformerKernel
{
    protected array $transformers = [
        'IDoNotExist',
    ];
}
