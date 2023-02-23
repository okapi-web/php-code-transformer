<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\TransformerDoesNotExistKernel;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\TransformerDoesNotExtendTransformerKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class InvalidTransformerClassTest extends TestCase
{
    public function testTransformerNotFound(): void
    {
        $this->expectException(TransformerNotFoundException::class);

        TransformerDoesNotExistKernel::init(cacheDir: Util::CACHE_DIR);
    }

    public function testTransformerDoesNotExtendTransformer(): void
    {
        $this->expectException(InvalidTransformerClassException::class);

        TransformerDoesNotExtendTransformerKernel::init(cacheDir: Util::CACHE_DIR);
    }
}
