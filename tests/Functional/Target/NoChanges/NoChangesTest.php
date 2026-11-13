<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\NoChanges;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
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
        Kernel::init();

        $class = Target::class;
        $this->assertWillBeTransformed($class);

        new Target();
    }

    public function testCachedNoChangesClass(): void
    {
        Kernel::init();

        $class = Target::class;
        $this->assertTransformerNotApplied($class);

        new Target();

        $originalFilePath = Util::getFilePath(Target::class);
        $cachedFilePath = Util::getTransformedFilePath(Target::class);
        static::assertFileExists($originalFilePath);
        static::assertFileDoesNotExist($cachedFilePath);
    }
}
