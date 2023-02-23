<?php
/** @noinspection ALL */
namespace Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;

class MultipleTransformersClass
{
    private string $privateProperty = "You can't get me!";

    private function test(): string
    {
        return 'Hello World!';
    }
}
