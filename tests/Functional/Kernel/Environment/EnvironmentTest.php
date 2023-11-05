<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\Environment;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Kernel\DevelopmentEnvironmentKernel;
use Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Kernel\ProductionEnvironmentKernel;
use Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Target\HelloWorld;
use Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Throwable;

#[RunTestsInSeparateProcesses]
class EnvironmentTest extends TestCase
{
    use ClassLoaderMockTrait;

    /**
     * @see StringTransformer::transform()
     */
    public function testDevelopmentEnvironment(): void
    {
        Util::clearCache();
        DevelopmentEnvironmentKernel::init();

        $class = HelloWorld::class;
        $this->assertWillBeTransformed($class);

        $helloWorldClass = new $class();

        $this->assertSame(
            'Hello from Code Transformer!',
            $helloWorldClass->say(),
        );
    }

    /**
     * @see StringTransformer::transform()
     */
    public function testCachedDevelopmentEnvironment(): void
    {
        DevelopmentEnvironmentKernel::init();

        $class = HelloWorld::class;
        $this->assertTransformerLoadedFromCache($class);

        $helloWorldClass = new $class();

        $this->assertSame(
            'Hello from Code Transformer!',
            $helloWorldClass->say(),
        );
    }

    /**
     * @see StringTransformer::transform()
     */
    public function testChangedDevelopmentEnvironment(): void
    {
        // Change class
        $classFilePath            = Util::getFilePath(HelloWorld::class);
        $originalClassFileContent = Filesystem::readFile($classFilePath);

        $changedFileContent = str_replace(
            'Hello World!',
            'Hello Changed World!',
            $originalClassFileContent,
        );

        sleep(1);
        Filesystem::writeFile($classFilePath, $changedFileContent);

        $exception = null;
        try {
            DevelopmentEnvironmentKernel::init();

            $class = HelloWorld::class;
            $this->assertWillBeTransformed($class);

            $classInstance = new $class();
            $this->assertSame(
                'Hello Changed World from Code Transformer!',
                $classInstance->say(),
            );
        } catch (Throwable $e) {
            $exception = $e;
        }

        // Restore class
        Filesystem::writeFile($classFilePath, $originalClassFileContent);

        if ($exception !== null) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $exception;
        }
    }

    /**
     * @see StringTransformer::transform()
     */
    public function testProductionEnvironment(): void
    {
        Util::clearCache();
        ProductionEnvironmentKernel::init();

        $class = HelloWorld::class;
        $this->assertWillBeTransformed($class);

        $helloWorldClass = new $class();

        $this->assertSame(
            'Hello from Code Transformer!',
            $helloWorldClass->say(),
        );
    }

    /**
     * @see StringTransformer::transform()
     */
    public function testCachedProductionEnvironment(): void
    {
        ProductionEnvironmentKernel::init();

        $class = HelloWorld::class;
        $this->assertTransformerLoadedFromCache($class);

        $helloWorldClass = new $class();

        $this->assertSame(
            'Hello from Code Transformer!',
            $helloWorldClass->say(),
        );
    }

    /**
     * @see StringTransformer::transform()
     */
    public function testChangedProductionEnvironment(): void
    {
        // Change class
        $classFilePath            = Util::getFilePath(HelloWorld::class);
        $originalClassFileContent = Filesystem::readFile($classFilePath);

        $changedFileContent = str_replace(
            'Hello World!',
            'Hello Changed World!',
            $originalClassFileContent,
        );

        sleep(1);
        Filesystem::writeFile($classFilePath, $changedFileContent);

        $exception = null;
        try {
            ProductionEnvironmentKernel::init();

            $class = HelloWorld::class;
            $this->assertTransformerLoadedFromCache($class);

            $classInstance = new $class();
            $this->assertSame(
                'Hello from Code Transformer!',
                $classInstance->say(),
            );
        } catch (Throwable $e) {
            $exception = $e;
        }

        // Restore class
        Filesystem::writeFile($classFilePath, $originalClassFileContent);

        if ($exception !== null) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $exception;
        }
    }
}
