<?php
/** @noinspection PhpUnhandledExceptionInspection */
namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Transformer;

use Microsoft\PhpParser\Node\Expression\AssignmentExpression;
use Microsoft\PhpParser\Node\Expression\Variable;
use Microsoft\PhpParser\Node\Statement\ExpressionStatement;
use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Target\AddedTransformerClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class AddedTransformer1 extends Transformer
{
    public function getTargetClass(): string|array
    {
        return AddedTransformerClass::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->getSourceFileNode();

        foreach ($sourceFileNode->getDescendantNodes() as $node) {
            if ($node instanceof ExpressionStatement) {
                assert($node->expression instanceof AssignmentExpression);
                $leftOperand = $node->expression->leftOperand;
                assert($leftOperand instanceof Variable);
                $leftOperandValue = $leftOperand->getText();
                $rightOperand = $node->expression->rightOperand;
                assert($rightOperand instanceof StringLiteral);

                if ($leftOperandValue === '$world') {
                    $code->editAt(
                        $rightOperand->getStartPosition() + 1,
                        $rightOperand->getWidth() - 2,
                        'Code Transformer'
                    );
                }
            }
        }
    }
}
