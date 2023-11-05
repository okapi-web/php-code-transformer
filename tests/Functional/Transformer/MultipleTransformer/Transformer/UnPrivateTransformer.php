<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Transformer;

use Microsoft\PhpParser\TokenKind;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Target\MultipleTransformersClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class UnPrivateTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return MultipleTransformersClass::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->getSourceFileNode();

        foreach ($sourceFileNode->getDescendantTokens() as $token) {
            if ($token->kind === TokenKind::PrivateKeyword) {
                $code->edit($token, 'public');
            }
        }
    }
}
