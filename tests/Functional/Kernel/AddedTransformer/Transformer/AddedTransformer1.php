<?php

/** @noinspection PhpUnhandledExceptionInspection */
namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Transformer;

use Microsoft\PhpParser\Node\Expression\AssignmentExpression;
use Microsoft\PhpParser\Node\Expression\Variable;
use Microsoft\PhpParser\Node\Statement\ExpressionStatement;
use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Target;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;
use PHPUnit\Framework\Assert;

class AddedTransformer1 extends Transformer
{
    public function getTargetClass(): string|array
    {
        return Target::class;
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->getSourceFileNode();

        /** @var iterable<\Microsoft\PhpParser\Node> $nodes */
        $nodes = $sourceFileNode->getDescendantNodes();
        foreach ($nodes as $node) {
            if (!$node instanceof ExpressionStatement) {
                continue;
            }

            $expression = $node->expression;
            Assert::assertInstanceOf(
                AssignmentExpression::class,
                $expression,
                'The parser node must match this fixture.',
            );
            $leftOperand = $expression->leftOperand;
            Assert::assertInstanceOf(Variable::class, $leftOperand, 'The parser node must match this fixture.');
            $leftOperandValue = $leftOperand->getText();
            $rightOperand = $expression->rightOperand;
            Assert::assertInstanceOf(StringLiteral::class, $rightOperand, 'The parser node must match this fixture.');

            if ($leftOperandValue === '$world') {
                $code->editAt($rightOperand->getStartPosition() + 1, $rightOperand->getWidth() - 2, 'Code Transformer');
            }
        }
    }
}
