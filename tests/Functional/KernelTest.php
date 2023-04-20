<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class KernelTest extends TestCase
{
    public function testKernel(): void
    {
        Util::clearCache();

        $this->assertFalse(ApplicationKernel::isInitialized());
        ApplicationKernel::init();
        $this->assertTrue(ApplicationKernel::isInitialized());

        $this->assertFileDoesNotExist(Util::CACHE_STATES_FILE);
    }

    public function testCachedKernel(): void
    {
        $this->assertFalse(ApplicationKernel::isInitialized());
        ApplicationKernel::init();
        $this->assertTrue(ApplicationKernel::isInitialized());

        $this->assertFileExists(Util::CACHE_STATES_FILE);
    }
}
