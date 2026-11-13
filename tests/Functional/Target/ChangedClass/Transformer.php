<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\ChangedClass;

use Microsoft\PhpParser\Node\StringLiteral;
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
        $sourceFileNode = $code->getSourceFileNode();

        /** @var iterable<\Microsoft\PhpParser\Node> $nodes */
        $nodes = $sourceFileNode->getDescendantNodes();
        foreach ($nodes as $node) {
            // Find 'Hello World!' string
            if (!$node instanceof StringLiteral) {
                continue;
            }

            if ($node->getStringContentsText() === 'Hello World!') {
                // Replace it with 'Hello World from Code Transformer!'
                $code->edit($node, "'Hello World from Code Transformer!'");
                continue;
            }
            if ($node->getStringContentsText() === 'Hello Changed World!') {
                // Replace it with 'Hello Changed World from Code Transformer!'
                $code->edit($node, "'Hello Changed World from Code Transformer!'");
            }
        }
    }
}
