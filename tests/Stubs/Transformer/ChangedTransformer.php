<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\ChangedClass;
use Okapi\CodeTransformer\Transformer;

class ChangedTransformer extends Transformer
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
                    // Replace it with 'Hello from Code Transformer!'
                    $code->edit(
                        $node->children,
                        "'Hello from Code Transformer!'",
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
