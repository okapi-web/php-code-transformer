<?php
/** @noinspection ALL */
namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer;

class TargetClass
{
    private string $privateProperty = "You can't get me!";

    private function test(): string
    {
        return 'Hello World!';
    }
}
