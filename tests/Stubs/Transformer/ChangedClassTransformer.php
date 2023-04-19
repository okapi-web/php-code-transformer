<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\ChangedClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class ChangedClassTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return ChangedClass::class;
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
