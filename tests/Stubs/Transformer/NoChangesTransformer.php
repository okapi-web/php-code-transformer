<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\NoChangesClass;
use Okapi\CodeTransformer\Transformer;

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
