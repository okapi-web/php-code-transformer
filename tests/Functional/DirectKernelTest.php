<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Exception\Kernel\DirectKernelInitializationException;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\TestCase;

class DirectKernelTest extends TestCase
{
    public function testDirectKernelInitialization(): void
    {
        $this->expectException(DirectKernelInitializationException::class);

        CodeTransformerKernel::init();
    }
}
