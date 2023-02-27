<?php

namespace Okapi\CodeTransformer\Tests\Functional\Workflow;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\CachedKernel;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Throwable;

/**
 * This test has to be run after ApplicationTest.
 */
class CachedApplicationTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testKernel(): void
    {
        $this->assertFalse(CachedKernel::isInitialized());
        CachedKernel::init(cacheDir: Util::CACHE_DIR);
        $this->assertTrue(CachedKernel::isInitialized());

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

    /**
     * Cached by {@see ApplicationTest::testStringClass()}
     */
    public function testStringClass(): void
    {
        $class = ClassesToTransform\StringClass::class;
        $this->assertTransformerLoadedFromCache($class);

        $stringClass = new $class();
        $this->assertSame('Hello from Code Transformer!', $stringClass->test());
    }

    /**
     * Cached by {@see ApplicationTest::testNoChangesClass()}
     */
    public function testNoChangesClass(): void
    {
        $class = ClassesToTransform\NoChangesClass::class;
        $this->assertTransformerNotApplied($class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $stringClass = new $class();

        $originalFilePath = __DIR__ . '/../../Stubs/ClassesToTransform/NoChangesClass.php';
        $cachedFilePath = Util::CACHE_DIR . '/transformed/tests/Stubs/ClassesToTransform/NoChangesClass.php';
        $this->assertFileExists($originalFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);
    }

    /**
     * Cached by {@see ApplicationTest::testChangedClass()}
     */
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

            $class = ClassesToTransform\ChangedClass::class;
            $this->assertWillBeTransformed($class);

            $changedClass = new $class();
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

    /**
     * Cached by {@see ApplicationTest::testDeleteCacheFileClass()}
     */
    public function testDeleteCacheFileClass(): void
    {
        $cachedFilePath = Util::CACHE_DIR . '/transformed/tests/Stubs/ClassesToTransform/DeleteCacheFileClass.php';

        $this->assertFileExists($cachedFilePath);
        Filesystem::rm($cachedFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);

        $class = ClassesToTransform\DeleteCacheFileClass::class;
        $this->assertWillBeTransformed($class);

        $deleteCacheFileClass = new $class();
        $this->assertSame('Hello World!', $deleteCacheFileClass->test());

        $this->assertFileExists($cachedFilePath);
    }

    /**
     * Cached by {@see ApplicationTest::testMultipleTransformers()}
     */
    public function testMultipleTransformers(): void
    {
        $class = ClassesToTransform\MultipleTransformersClass::class;
        $this->assertTransformerLoadedFromCache($class);

        $multipleTransformersClass = new $class();
        $this->assertSame('Hello from Code Transformer!', $multipleTransformersClass->test());
        $this->assertSame("You can't get me!", $multipleTransformersClass->privateProperty);
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

    public function testClearCache(): void
    {
        Util::clearCache();

        $this->expectNotToPerformAssertions();
    }
}
