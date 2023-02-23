<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\StringClass;

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
