<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\NoChangesClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class NoChangesClassTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testNoChangesClass(): void
    {
        Util::clearCache();
        ApplicationKernel::init();

        $class = NoChangesClass::class;
        $this->assertWillBeTransformed($class);

        new $class();
    }

    public function testCachedNoChangesClass(): void
    {
        ApplicationKernel::init();

        $class = NoChangesClass::class;
        $this->assertTransformerNotApplied($class);

        new $class();

        $originalFilePath = Util::CLASSES_TO_TRANSFORM_DIR . '/NoChangesClass.php';
        $cachedFilePath   = Util::CACHED_CLASSES_TO_TRANSFORM_DIR . '/NoChangesClass.php';
        $this->assertFileExists($originalFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);
    }
}
