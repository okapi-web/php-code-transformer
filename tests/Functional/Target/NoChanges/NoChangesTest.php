<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\NoChanges;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Target\NoChanges\Kernel\NoChangesKernel;
use Okapi\CodeTransformer\Tests\Functional\Target\NoChanges\Target\NoChangesClass;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class NoChangesTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testNoChangesClass(): void
    {
        Util::clearCache();
        NoChangesKernel::init();

        $class = NoChangesClass::class;
        $this->assertWillBeTransformed($class);

        new $class();
    }

    public function testCachedNoChangesClass(): void
    {
        NoChangesKernel::init();

        $class = NoChangesClass::class;
        $this->assertTransformerNotApplied($class);

        new $class();

        $originalFilePath = Util::getFilePath(NoChangesClass::class);
        $cachedFilePath   = Util::getTransformedFilePath(NoChangesClass::class);
        $this->assertFileExists($originalFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);
    }
}
