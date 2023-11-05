<?php

namespace Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor;

use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Target\StringClass;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class CachedFileAndDestructorTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testReplaceStringInClass(): void
    {
        Util::clearCache();
        Kernel::init();

        $class = StringClass::class;
        $this->assertWillBeTransformed($class);

        $stringClass = new $class();
        $this->assertSame(
            'Hello from Code Transformer!',
            $stringClass->test(),
        );

        $file    = Util::getFilePath(StringClass::class);
        $content = Filesystem::readFile($file);

        $this->assertEquals(
            $content,
            StringTransformer::$originalSourceCode,
        );
    }

    public function testCachedReplaceStringClass(): void
    {
        Kernel::init();

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
        $cachedFilePath = Util::getTransformedFilePath(StringClass::class);

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
        Kernel::init();

        $class = StringClass::class;
        $stringClass = new $class();

        $cacheStateManager = DI::get(CacheStateManager::class);

        $this->assertFileDoesNotExist(Util::CACHE_STATES_FILE);
        $cacheStateManager->__destruct();
        $this->assertFileExists(Util::CACHE_STATES_FILE);

        $file = require Util::CACHE_STATES_FILE;

        $key = 'CODE_TRANSFORMER_APP_DIR\tests\Functional\Cache\CachedFileAndDestructor\Target\StringClass.php';
        $key = str_replace('\\', DIRECTORY_SEPARATOR, $key);
        $this->assertArrayHasKey($key, $file);
    }
}
