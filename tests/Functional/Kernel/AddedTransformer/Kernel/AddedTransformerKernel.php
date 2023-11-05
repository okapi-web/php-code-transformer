<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Kernel;

use Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Transformer\AddedTransformer2;

class AddedTransformerKernel extends BeforeAddedTransformerKernel
{
    protected array $addedTransformers = [
        AddedTransformer2::class,
    ];

    protected function configureOptions(): void
    {
        $this->transformerManager->addTransformers($this->addedTransformers);
    }
}
