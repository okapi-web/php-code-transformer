<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\SyntaxErrorClass;
use Okapi\CodeTransformer\Transformer;

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
