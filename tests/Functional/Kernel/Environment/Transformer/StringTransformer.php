<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Transformer;

use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Target\HelloWorld;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class StringTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return [HelloWorld::class];
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
                // Replace it with 'Hello from Code Transformer!'
                $code->edit($node, "'Hello from Code Transformer!'");
                continue;
            }
            if ($node->getStringContentsText() === 'Hello Changed World!') {
                // Replace it with 'Hello Changed World from Code Transformer!'
                $code->edit($node, "'Hello Changed World from Code Transformer!'");
            }
        }
    }
}
