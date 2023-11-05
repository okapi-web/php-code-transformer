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

        foreach ($sourceFileNode->getDescendantNodes() as $node) {
            // Find 'Hello World!' string
            if ($node instanceof StringLiteral) {
                if ($node->getStringContentsText() === 'Hello World!') {
                    // Replace it with 'Hello World from Code Transformer!'
                    $code->edit(
                        $node->children,
                        "'Hello World from Code Transformer!'",
                    );
                } else if ($node->getStringContentsText() === 'Hello Changed World!') {
                    // Replace it with 'Hello Changed World from Code Transformer!'
                    $code->edit(
                        $node->children,
                        "'Hello Changed World from Code Transformer!'",
                    );
                }
            }
        }
    }
}
