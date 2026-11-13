<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel;

use Okapi\CodeTransformer\Tests\Stubs\Kernel\EmptyKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class KernelTest extends TestCase
{
    public function testKernel(): void
    {
        Util::clearCache();

        static::assertFalse(EmptyKernel::isInitialized());
        EmptyKernel::init();
        static::assertTrue(EmptyKernel::isInitialized());

        static::assertFileDoesNotExist(Util::CACHE_STATES_FILE);
    }

    public function testCachedKernel(): void
    {
        static::assertFalse(EmptyKernel::isInitialized());
        EmptyKernel::init();
        static::assertTrue(EmptyKernel::isInitialized());

        static::assertFileExists(Util::CACHE_STATES_FILE);
    }
}
