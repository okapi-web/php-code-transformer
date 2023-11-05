<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Target;

class AddedTransformerClass
{
    public function test(): string
    {
        $hello = 'Hello';
        $world = 'World';

        return "$hello $world!";
    }
}
