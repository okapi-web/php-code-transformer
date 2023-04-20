<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\DeleteCacheFileClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
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
        ApplicationKernel::init();

        $class = DeleteCacheFileClass::class;
        $this->assertWillBeTransformed($class);

        $deleteCacheFileClass = new $class();
        $this->assertSame(
            'Hello World!',
            $deleteCacheFileClass->test(),
        );
    }

    public function testCachedDeleteCacheFileClass(): void
    {
        ApplicationKernel::init();

        $cachedFilePath = Util::CACHED_CLASSES_TO_TRANSFORM_DIR . '/DeleteCacheFileClass.php';

        $this->assertFileExists($cachedFilePath);
        Filesystem::rm($cachedFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);

        $class = DeleteCacheFileClass::class;
        $this->assertWillBeTransformed($class);

        $deleteCacheFileClass = new $class();
        $this->assertSame('Hello World!', $deleteCacheFileClass->test());

        $this->assertFileExists($cachedFilePath);
    }
}
