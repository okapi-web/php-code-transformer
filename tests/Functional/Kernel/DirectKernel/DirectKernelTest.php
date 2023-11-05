<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\DirectKernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Core\Exception\Kernel\DirectKernelInitializationException;
use PHPUnit\Framework\TestCase;

class DirectKernelTest extends TestCase
{
    public function testDirectKernelInitialization(): void
    {
        $this->expectException(DirectKernelInitializationException::class);

        CodeTransformerKernel::init();
    }
}
