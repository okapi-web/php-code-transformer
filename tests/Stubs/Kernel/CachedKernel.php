<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Okapi\CodeTransformer\Service\TransformerContainer;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\AddedTransformer2;

class CachedKernel extends ApplicationKernel
{
    protected array $addedTransformers = [
        AddedTransformer2::class,
    ];

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct()
    {
        TransformerContainer::addTransformers($this->addedTransformers);
    }
}
