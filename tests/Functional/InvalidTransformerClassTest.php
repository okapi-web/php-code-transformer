<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Core\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\TransformerDoesNotExistKernel;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\TransformerDoesNotExtendTransformerKernel;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class InvalidTransformerClassTest extends TestCase
{
    public function testTransformerNotFound(): void
    {
        $this->expectException(TransformerNotFoundException::class);

        TransformerDoesNotExistKernel::init();
    }

    public function testTransformerDoesNotExtendTransformer(): void
    {
        $this->expectException(InvalidTransformerClassException::class);

        TransformerDoesNotExtendTransformerKernel::init();
    }
}
