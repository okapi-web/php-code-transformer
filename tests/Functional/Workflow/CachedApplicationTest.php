<?php

namespace Okapi\CodeTransformer\Tests\Functional\Workflow;

use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * This test has to be run after ApplicationTest.
 */
class CachedApplicationTest extends TestCase
{
    public function testKernel(): void
    {
        \Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel::init(
            cacheDir: Util::CACHE_DIR,
        );

        $this->assertTrue(\Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel::isInitialized());

        $this->assertFileExists(Util::CACHE_STATES_FILE);
    }

    public function testCachedFile(): void
    {
        $cachedFilePath = Util::CACHE_DIR . '/transformed/tests/Stubs/ClassesToTransform/StringClass.php';

        $this->assertFileExists($cachedFilePath);
        $file = Filesystem::readFile($cachedFilePath);

        $this->assertStringContainsString('Hello from Code Transformer!', $file);
        $this->assertStringContainsString('$iAmAppended = true;', $file);
    }

    public function testStringClass(): void
    {
        $stringClass = new ClassesToTransform\StringClass();
        $this->assertSame('Hello from Code Transformer!', $stringClass->test());
    }

    public function testNoChangesClass(): void
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $stringClass = new ClassesToTransform\StringClass();

        $originalFilePath = __DIR__ . '/../../Stubs/ClassesToTransform/NoChangesClass.php';
        $cachedFilePath = Util::CACHE_DIR . '/transformed/tests/Stubs/ClassesToTransform/NoChangesClass.php';
        $this->assertFileExists($originalFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);
    }

    public function testChangedClass(): void
    {
        $originalFilePath = __DIR__ . '/../../Stubs/ClassesToTransform/ChangedClass.php';

        $originalFileContent = Filesystem::readFile($originalFilePath);

        $exception = null;
        try {
            $changedFileContent = str_replace(
                'Hello World!',
                'Hello Changed World!',
                $originalFileContent,
            );

            Filesystem::writeFile($originalFilePath, $changedFileContent);

            $changedClass = new ClassesToTransform\ChangedClass();
            $this->assertSame('Hello Changed World from Code Transformer!', $changedClass->test());
        } catch (Throwable $e) {
            $exception = $e;
        }

        Filesystem::writeFile($originalFilePath, $originalFileContent);

        if ($exception !== null) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $exception;
        }
    }

    public function testDeleteCacheFileClass(): void
    {
        $cachedFilePath = Util::CACHE_DIR . '/transformed/tests/Stubs/ClassesToTransform/DeleteCacheFileClass.php';

        $this->assertFileExists($cachedFilePath);
        Filesystem::rm($cachedFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);

        $deleteCacheFileClass = new ClassesToTransform\DeleteCacheFileClass();
        $this->assertSame('Hello World!', $deleteCacheFileClass->test());

        $this->assertFileExists($cachedFilePath);
    }

    public function testClearCache(): void
    {
        Util::clearCache();

        $this->expectNotToPerformAssertions();
    }
}
