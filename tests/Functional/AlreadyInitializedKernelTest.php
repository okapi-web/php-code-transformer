<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\Stubs\Kernel\EmptyKernel;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ExceptionOnDoubleInitializationKernel;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Singleton\Exceptions\AlreadyInitializedException;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class AlreadyInitializedKernelTest extends TestCase
{
    public function testInitializedKernelTwice(): void
    {
        Util::clearCache();

        EmptyKernel::init();
        EmptyKernel::init();

        $this->assertTrue(true);
    }

    public function testInitializeKernelTwiceWithExceptionOnDoubleInitializationOption(): void
    {
        Util::clearCache();

        $this->expectException(AlreadyInitializedException::class);

        ExceptionOnDoubleInitializationKernel::init();
        ExceptionOnDoubleInitializationKernel::init();
    }
}
