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

        $this->assertFalse(EmptyKernel::isInitialized());
        EmptyKernel::init();
        $this->assertTrue(EmptyKernel::isInitialized());

        $this->assertFileDoesNotExist(Util::CACHE_STATES_FILE);
    }

    public function testCachedKernel(): void
    {
        $this->assertFalse(EmptyKernel::isInitialized());
        EmptyKernel::init();
        $this->assertTrue(EmptyKernel::isInitialized());

        $this->assertFileExists(Util::CACHE_STATES_FILE);
    }
}
