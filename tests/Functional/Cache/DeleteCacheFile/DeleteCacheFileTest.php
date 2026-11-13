<?php

namespace Okapi\CodeTransformer\Tests\Functional\Cache\DeleteCacheFile;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class DeleteCacheFileTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testDeleteCacheFileClass(): void
    {
        Util::clearCache();
        Kernel::init();

        $class = Target::class;
        $this->assertWillBeTransformed($class);

        $deleteCacheFileClass = new Target();
        static::assertSame('Hello World!', $deleteCacheFileClass->test());
    }

    public function testCachedDeleteCacheFileClass(): void
    {
        Kernel::init();

        $cachedFilePath = Util::getTransformedFilePath(Target::class);

        static::assertFileExists($cachedFilePath);
        Filesystem::rm($cachedFilePath);
        static::assertFileDoesNotExist($cachedFilePath);

        $class = Target::class;
        $this->assertWillBeTransformed($class);

        $deleteCacheFileClass = new Target();
        static::assertSame('Hello World!', $deleteCacheFileClass->test());

        static::assertFileExists($cachedFilePath);
    }
}
