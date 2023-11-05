<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass\Transformer;

use Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass\Target\SyntaxErrorClass;
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

        $refClass = $code->getReflectionClass();
        assert($refClass->getName() === SyntaxErrorClass::class);

        $className = $code->getClassName();
        assert($className === 'SyntaxErrorClass');
    }
}
