<?php
/** @noinspection ALL */
namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Target;

class MultipleTransformersClass
{
    private string $privateProperty = "You can't get me!";

    private function test(): string
    {
        return 'Hello World!';
    }
}
