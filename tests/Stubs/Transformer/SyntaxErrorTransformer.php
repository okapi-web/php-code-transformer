<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\SyntaxErrorClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class SyntaxErrorTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return SyntaxErrorClass::class;
    }

    public function transform(Code $code): void
    {
        $code->append('}');
    }
}
