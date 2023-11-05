<?php

namespace Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer;

use Microsoft\PhpParser\Node\StringLiteral;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Target\StringClass;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Target\MultipleTransformersClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

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
