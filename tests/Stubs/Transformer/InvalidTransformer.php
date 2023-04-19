<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\StringClass;
use Okapi\CodeTransformer\Transformer\Code;

class InvalidTransformer
{
    public function getTargetClass(): string|array
    {
        return StringClass::class;
    }

    public function transform(Code $code): void
    {
        // Do nothing
    }
}
