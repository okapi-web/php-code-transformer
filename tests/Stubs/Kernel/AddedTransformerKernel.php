<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Okapi\CodeTransformer\Tests\Stubs\Transformer\AddedTransformer2;

class AddedTransformerKernel extends ApplicationKernel
{
    protected array $addedTransformers = [
        AddedTransformer2::class,
    ];

    protected function preInit(): void
    {
        parent::preInit();

        $this->transformerContainer->addTransformers($this->addedTransformers);
    }
}
