<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass;

use Okapi\CodeTransformer\Transformer as TransformerClass;
use Okapi\CodeTransformer\Transformer\Code;

class Transformer extends TransformerClass
{
    public function getTargetClass(): string|array
    {
        return Target::class;
    }

    public function transform(Code $code): void
    {
        $code->append('}');

        $refClass = $code->getReflectionClass();
        assert($refClass->getName() === Target::class);

        $className = $code->getNewSource();
        assert($className === 'SyntaxErrorClass');
    }
}
