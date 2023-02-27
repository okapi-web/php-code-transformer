<?php

namespace Okapi\CodeTransformer\Tests\Functional\Workflow;

use Okapi\CodeTransformer\Exception\Transformer\SyntaxError;
use Okapi\CodeTransformer\Service\CacheStateManager;
use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunClassInSeparateProcess;
use PHPUnit\Framework\TestCase;

#[RunClassInSeparateProcess]
class ApplicationTest extends TestCase
{
    use ClassLoaderMockTrait;

    /**
     * Cached test in {@see CachedApplicationTest::testKernel()}
     */
    public function testKernel(): void
    {
        Util::clearCache();

        $this->assertFalse(ApplicationKernel::isInitialized());
        ApplicationKernel::init(cacheDir: Util::CACHE_DIR);
        $this->assertTrue(ApplicationKernel::isInitialized());

        $this->assertFileDoesNotExist(Util::CACHE_STATES_FILE);
    }

    /**
     * Cached test in {@see CachedApplicationTest::testStringClass()}
     */
    public function testStringClass(): void
    {
        $class = ClassesToTransform\StringClass::class;
        $this->assertWillBeTransformed($class);

        $stringClass = new $class();
        $this->assertSame('Hello from Code Transformer!', $stringClass->test());

        $file = __DIR__ . '/../../Stubs/ClassesToTransform/StringClass.php';
        $content = Filesystem::readFile($file);

        $this->assertEquals($content, StringTransformer::$originalSourceCode);
    }

    /**
     * Cached test in {@see CachedApplicationTest::testNoChangesClass()}
     */
    public function testNoChangesClass(): void
    {
        $class = ClassesToTransform\NoChangesClass::class;
        $this->assertWillBeTransformed($class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $noChangesClass = new $class();
    }

    public function testSyntaxErrorClass(): void
    {
        $this->expectException(SyntaxError::class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $syntaxErrorClass = new ClassesToTransform\SyntaxErrorClass();
    }

    /**
     * Cached test in {@see CachedApplicationTest::testChangedClass()}
     */
    public function testChangedClass(): void
    {
        $class = ClassesToTransform\ChangedClass::class;
        $this->assertWillBeTransformed($class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $changedClass = new $class();
        $this->assertSame('Hello from Code Transformer!', $changedClass->test());
    }

    /**
     * Cached test in {@see CachedApplicationTest::testDeleteCacheFileClass()}
     */
    public function testDeleteCacheFileClass(): void
    {
        $class = ClassesToTransform\DeleteCacheFileClass::class;
        $this->assertWillBeTransformed($class);

        $deleteCacheFileClass = new $class();
        $this->assertSame('Hello World!', $deleteCacheFileClass->test());
    }

    /**
     * Cached test in {@see CachedApplicationTest::testMultipleTransformers()}
     */
    public function testMultipleTransformers(): void
    {
        $class = ClassesToTransform\MultipleTransformersClass::class;
        $this->assertWillBeTransformed($class);

        $multipleTransformersClass = new $class();
        $this->assertSame('Hello from Code Transformer!', $multipleTransformersClass->test());
        $this->assertSame("You can't get me!", $multipleTransformersClass->privateProperty);
    }

    /**
     * Cached test in {@see CachedApplicationTest::testAddedTransformer()}
     */
    public function testAddedTransformer(): void
    {
        $class = ClassesToTransform\AddedTransformerClass::class;
        $this->assertWillBeTransformed($class);

        $addedTransformerClass = new $class();
        $this->assertSame('Hello Code Transformer!', $addedTransformerClass->test());
    }

    public function testDestructor(): void
    {
        $cacheStateManager = CacheStateManager::getInstance();

        $this->assertFileDoesNotExist(Util::CACHE_STATES_FILE);
        $cacheStateManager->__destruct();
        $this->assertFileExists(Util::CACHE_STATES_FILE);

        $file = require Util::CACHE_STATES_FILE;

        $key = 'CODE_TRANSFORMER_APP_DIR\tests\Stubs\ClassesToTransform\StringClass.php';
        $key = str_replace('\\', DIRECTORY_SEPARATOR, $key);
        $this->assertArrayHasKey($key, $file);

        $cachedFilePath = 'CODE_TRANSFORMER_APP_DIR\tests\cache\transformed\tests\Stubs\ClassesToTransform\StringClass.php';
        $cachedFilePath = str_replace('\\', DIRECTORY_SEPARATOR, $cachedFilePath);
        $this->assertArrayHasKey('cachedFilePath', $file[$key]);
        $this->assertEquals($cachedFilePath, $file[$key]['cachedFilePath']);
        $this->assertArrayHasKey('transformedTime', $file[$key]);
        $this->assertIsInt($file[$key]['transformedTime']);
        $this->assertArrayHasKey('transformerFilePaths', $file[$key]);
        $this->assertIsArray($file[$key]['transformerFilePaths']);
        $this->assertGreaterThan(0, count($file[$key]['transformerFilePaths']));
    }
}
