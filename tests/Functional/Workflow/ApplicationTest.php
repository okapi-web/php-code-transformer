<?php

namespace Okapi\CodeTransformer\Tests\Functional\Workflow;

use Okapi\CodeTransformer\Exception\Transformer\SyntaxError;
use Okapi\CodeTransformer\Service\CacheStateManager;
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
    public function testKernel(): void
    {
        Util::clearCache();

        $this->assertFalse(ApplicationKernel::isInitialized());
        ApplicationKernel::init(cacheDir: Util::CACHE_DIR);
        $this->assertTrue(ApplicationKernel::isInitialized());
    }

    public function testStringClass(): void
    {
        $stringClass = new ClassesToTransform\StringClass();
        $this->assertSame('Hello from Code Transformer!', $stringClass->test());

        $file = __DIR__ . '/../../Stubs/ClassesToTransform/StringClass.php';
        $content = Filesystem::readFile($file);

        $this->assertEquals($content, StringTransformer::$originalSourceCode);
    }

    /**
     * True test in {@see CachedApplicationTest::testNoChangesClass()}
     */
    public function testNoChangesClass(): void
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $noChangesClass = new ClassesToTransform\NoChangesClass();

        $this->expectNotToPerformAssertions();
    }

    public function testSyntaxErrorClass(): void
    {
        $this->expectException(SyntaxError::class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $syntaxErrorClass = new ClassesToTransform\SyntaxErrorClass();
    }

    /**
     * True test in {@see CachedApplicationTest::testChangedClass()}
     */
    public function testChangedClass(): void
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $changedClass = new ClassesToTransform\ChangedClass();
        $this->assertSame('Hello from Code Transformer!', $changedClass->test());
    }

    /**
     * True test in {@see CachedApplicationTest::testDeleteCacheFileClass()}
     */
    public function testDeleteCacheFileClass(): void
    {
        $deleteCacheFileClass = new ClassesToTransform\DeleteCacheFileClass();
        $this->assertSame('Hello World!', $deleteCacheFileClass->test());
    }

    public function testMultipleTransformers(): void
    {
        $multipleTransformersClass = new ClassesToTransform\MultipleTransformersClass();
        $this->assertSame('Hello from Code Transformer!', $multipleTransformersClass->test());
        $this->assertSame("You can't get me!", $multipleTransformersClass->privateProperty);
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
