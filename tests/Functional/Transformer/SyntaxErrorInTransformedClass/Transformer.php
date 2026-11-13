<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass;

use Okapi\CodeTransformer\Transformer as TransformerClass;
use Okapi\CodeTransformer\Transformer\Code;
use PHPUnit\Framework\Assert;

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
        Assert::assertSame(Target::class, $refClass->getName(), 'The reflected member must match this fixture.');

        $className = $code->getNewSource();
        Assert::assertSame('SyntaxErrorClass', $className, 'The reflected member must match this fixture.');
    }
}
