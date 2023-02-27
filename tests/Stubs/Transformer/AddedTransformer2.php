<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Microsoft\PhpParser\Node\Expression\AssignmentExpression;
use Microsoft\PhpParser\Node\Expression\Variable;
use Microsoft\PhpParser\Node\Statement\ExpressionStatement;
use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\AddedTransformerClass;
use Okapi\CodeTransformer\Transformer;

class AddedTransformer2 extends Transformer
{
    public function getTargetClass(): string|array
    {
        return AddedTransformerClass::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->sourceFileNode;

        foreach ($sourceFileNode->getDescendantNodes() as $node) {
            if ($node instanceof ExpressionStatement) {
                assert($node->expression instanceof AssignmentExpression);
                $leftOperand = $node->expression->leftOperand;
                assert($leftOperand instanceof Variable);
                $leftOperandValue = $leftOperand->getText();
                $rightOperand = $node->expression->rightOperand;
                assert($rightOperand instanceof StringLiteral);

                if ($leftOperandValue === '$hello') {
                    $code->editAt(
                        $rightOperand->getStartPosition() + 1,
                        $rightOperand->getWidth() - 2,
                        'Hello from'
                    );
                }
            }
        }
    }
}
