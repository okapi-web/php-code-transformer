<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\StringClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Stubs\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class ReplaceStringInClassTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testReplaceStringInClass(): void
    {
        Util::clearCache();
        ApplicationKernel::init();

        $class = StringClass::class;
        $this->assertWillBeTransformed($class);

        $stringClass = new $class();
        $this->assertSame(
            'Hello from Code Transformer!',
            $stringClass->test(),
        );

        $file    = Util::CLASSES_TO_TRANSFORM_DIR . '/StringClass.php';
        $content = Filesystem::readFile($file);

        $this->assertEquals(
            $content,
            StringTransformer::$originalSourceCode,
        );
    }

    public function testCachedReplaceStringClass(): void
    {
        ApplicationKernel::init();

        $class = StringClass::class;
        $this->assertTransformerLoadedFromCache($class);

        $stringClass = new $class();
        $this->assertSame(
            'Hello from Code Transformer!',
            $stringClass->test(),
        );
    }

    public function testCachedFile(): void
    {
        $cachedFilePath = Util::CACHED_CLASSES_TO_TRANSFORM_DIR . '/StringClass.php';

        $this->assertFileExists($cachedFilePath);
        $file = Filesystem::readFile($cachedFilePath);

        $this->assertStringContainsString(
            'Hello from Code Transformer!',
            $file,
        );

        $this->assertStringContainsString(
            '$iAmAppended = true;',
            $file,
        );
    }

    public function testDestructor(): void
    {
        Util::clearCache();
        ApplicationKernel::init();

        $class = StringClass::class;
        $stringClass = new $class();

        $cacheStateManager = DI::get(CacheStateManager::class);

        $this->assertFileDoesNotExist(Util::CACHE_STATES_FILE);
        $cacheStateManager->__destruct();
        $this->assertFileExists(Util::CACHE_STATES_FILE);

        $file = require Util::CACHE_STATES_FILE;

        $key = 'CODE_TRANSFORMER_APP_DIR\tests\Stubs\ClassesToTransform\StringClass.php';
        $key = str_replace('\\', DIRECTORY_SEPARATOR, $key);
        $this->assertArrayHasKey($key, $file);
    }
}
