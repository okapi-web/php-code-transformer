<?php

namespace Okapi\CodeTransformer\Tests\Functional\Workflow;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;

/**
 * This test has to be run after ApplicationTest.
 */
#[RunClassInSeparateProcess]
class B_CachedApplicationTest extends TestCase
{
    use ClassLoaderMockTrait;

    private static string $classFileContent;
    private static string $transformerFileContent;

    public static function setUpBeforeClass(): void
    {
        // Change files

        $classFilePath = __DIR__ . '/../../Stubs/ClassesToTransform/ChangedClass.php';
        self::$classFileContent = Filesystem::readFile($classFilePath);

        $changedFileContent = str_replace(
            'Hello World!',
            'Hello Changed World!',
            self::$classFileContent,
        );

        usleep(500 * 1000);
        Filesystem::writeFile($classFilePath, $changedFileContent);



        $transformerFilePath = __DIR__ . '/../../Stubs/Transformer/ChangedTransformerTransformer.php';
        self::$transformerFileContent = Filesystem::readFile($transformerFilePath);

        $changedFileContent = str_replace(
            'Hello World from Code Transformer!',
            'Hello Changed World from Code Transformer!',
            self::$transformerFileContent,
        );

        usleep(500 * 1000);
        Filesystem::writeFile($transformerFilePath, $changedFileContent);
    }

    public static function tearDownAfterClass(): void
    {
        // Restore files

        $classFilePath = __DIR__ . '/../../Stubs/ClassesToTransform/ChangedClass.php';
        Filesystem::writeFile($classFilePath, self::$classFileContent);

        $transformerFilePath = __DIR__ . '/../../Stubs/Transformer/ChangedTransformerTransformer.php';
        Filesystem::writeFile($transformerFilePath, self::$transformerFileContent);
    }

    public function testKernel(): void
    {
        $this->assertFalse(ApplicationKernel::isInitialized());
        ApplicationKernel::init();
        $this->assertTrue(ApplicationKernel::isInitialized());

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
        $cachedFilePath   = Util::CACHE_DIR . '/transformed/tests/Stubs/ClassesToTransform/NoChangesClass.php';
        $this->assertFileExists($originalFilePath);
        $this->assertFileDoesNotExist($cachedFilePath);
    }

    /**
     * Cached by {@see ApplicationTest::testChangedClass()}
     */
    public function testChangedClass(): void
    {
        $class = ClassesToTransform\ChangedClass::class;
        $this->assertWillBeTransformed($class);

        $changedClass = new $class();
        $this->assertSame(
            'Hello Changed World from Code Transformer!',
            $changedClass->test(),
        );
    }

    /**
     * Cached by {@see ApplicationTest::testChangedTransformer()}
     */
    public function testChangedTransformer(): void
    {
        $class = ClassesToTransform\ChangedTransformer::class;
        $this->assertWillBeTransformed($class);

        $classInstance = new $class;
        $this->assertSame(
            'Hello Changed World from Code Transformer!',
            $classInstance->test(),
        );
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
}
