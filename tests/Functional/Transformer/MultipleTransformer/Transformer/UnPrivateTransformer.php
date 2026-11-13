<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Transformer;

use Microsoft\PhpParser\TokenKind;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\TargetClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class UnPrivateTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return TargetClass::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->getSourceFileNode();

        /** @var iterable<\Microsoft\PhpParser\Token> $tokens */
        $tokens = $sourceFileNode->getDescendantTokens();
        foreach ($tokens as $token) {
            if ($token->kind !== TokenKind::PrivateKeyword) {
                continue;
            }

            $code->edit($token, 'public');
        }
    }
}
