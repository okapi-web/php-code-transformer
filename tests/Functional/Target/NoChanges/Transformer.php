<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\NoChanges;

use Okapi\CodeTransformer\Transformer as TransformerClass;
use Okapi\CodeTransformer\Transformer\Code;

class Transformer extends TransformerClass
{
    public function getTargetClass(): string|array
    {
        return [Target::class];
    }

    public function transform(Code $code): void
    {
        // No changes
    }
}
