<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer;

class Target
{
    public function test(): string
    {
        $hello = 'Hello';
        $world = 'World';

        return "$hello $world!";
    }
}
