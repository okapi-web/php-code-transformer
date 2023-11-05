<?php

namespace Okapi\CodeTransformer\Tests\Functional\Target\ChangedClass;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Target\ChangedClass\Kernel\ChangedClassKernel;
use Okapi\CodeTransformer\Tests\Functional\Target\ChangedClass\Target\ChangedClass;
use Okapi\CodeTransformer\Tests\Util;
use Okapi\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use Throwable;

#[RunTestsInSeparateProcesses]
class ChangedClassTest extends TestCase
{
    use ClassLoaderMockTrait;

    private static string $classFileContent;

    public function testChangedClass(): void
    {
        Util::clearCache();
        ChangedClassKernel::init();

        $class = ChangedClass::class;
        $this->assertWillBeTransformed($class);

        $changedClass = new $class();
        $this->assertSame(
            'Hello World from Code Transformer!',
            $changedClass->test(),
        );
    }

    public function testCachedChangedClass(): void
    {
        // Change class
        $classFilePath            = Util::getFilePath(ChangedClass::class);
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
            ChangedClassKernel::init();

            $class = ChangedClass::class;
            $this->assertWillBeTransformed($class);

            $classInstance = new $class();
            $this->assertSame(
                'Hello Changed World from Code Transformer!',
                $classInstance->test(),
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
