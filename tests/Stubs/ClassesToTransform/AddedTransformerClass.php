<?php

namespace Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;

class AddedTransformerClass
{
    public function test(): string
    {
        $hello = 'Hello';
        $world = 'World';

        return "$hello $world!";
    }
}
