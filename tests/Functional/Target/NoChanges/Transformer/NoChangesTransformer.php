<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\NoChanges\Transformer;

use Okapi\CodeTransformer\Tests\Functional\Target\NoChanges\Target\NoChangesClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class NoChangesTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return [NoChangesClass::class];
    }

    public function transform(Code $code): void
    {
        // No changes
    }
}
