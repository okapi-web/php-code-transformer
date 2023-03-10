<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Microsoft\PhpParser\TokenKind;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\MultipleTransformersClass;
use Okapi\CodeTransformer\Transformer;

class UnPrivateTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return MultipleTransformersClass::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->sourceFileNode;

        foreach ($sourceFileNode->getDescendantTokens() as $token) {
            if ($token->kind === TokenKind::PrivateKeyword) {
                $code->edit($token, 'public');
            }
        }
    }
}
