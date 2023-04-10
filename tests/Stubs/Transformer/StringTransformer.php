<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\MultipleTransformersClass;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\StringClass;
use Okapi\CodeTransformer\Transformer;

class StringTransformer extends Transformer
{
    public static ?string $originalSourceCode = null;

    public function getTargetClass(): string|array
    {
        return [StringClass::class, 'Okapi*Tests*StringClass', MultipleTransformersClass::class];
    }

    public function transform(Code $code): void
    {
        $sourceFileNode = $code->getSourceFileNode();

        foreach ($sourceFileNode->getDescendantNodes() as $node) {
            // Find 'Hello World!' string
            if ($node instanceof StringLiteral
                && $node->getStringContentsText() === 'Hello World!'
            ) {
                // Replace it with 'Hello from Code Transformer!'
                $code->edit(
                    $node->children,
                    "'Hello from Code Transformer!'",
                );



                $code->append('$iAmAppended = true;');
            }
        }

        self::$originalSourceCode = $code->getOriginalSource();
    }
}
