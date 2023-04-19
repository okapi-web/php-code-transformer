<?php

namespace Okapi\CodeTransformer\Tests\Functional\Workflow;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\AddedTransformerKernel;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * This test has to be run after CachedApplicationTest.
 */
#[RunClassInSeparateProcess]
class C_AddedTransformerTest extends TestCase
{
    use ClassLoaderMockTrait;

    public static function tearDownAfterClass(): void
    {
        Util::clearCache();
    }

    public function testKernel(): void
    {
        $this->assertFalse(AddedTransformerKernel::isInitialized());
        ApplicationKernel::init();
        $this->assertTrue(ApplicationKernel::isInitialized());

        $this->assertFileExists(Util::CACHE_STATES_FILE);
    }

    /**
     * Cached by {@see ApplicationTest::testAddedTransformer()}
     */
    public function testAddedTransformer(): void
    {
        $class = ClassesToTransform\AddedTransformerClass::class;
        $this->assertWillBeTransformed($class);

        $addedTransformerClass = new $class();
        $this->assertSame('Hello from Code Transformer!', $addedTransformerClass->test());
    }
}
