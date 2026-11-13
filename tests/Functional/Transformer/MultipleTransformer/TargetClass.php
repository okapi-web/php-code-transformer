<?php

/** @noinspection ALL */
namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer;

class TargetClass
{
    private string $privateProperty = "You can't get me!";

    /** @return array{string, string} */
    public function snapshot(): array
    {
        return [$this->test(), $this->privateProperty];
    }

    private function test(): string
    {
        return 'Hello World!';
    }
}
