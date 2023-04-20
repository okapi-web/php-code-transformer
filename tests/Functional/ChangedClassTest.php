<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\ChangedClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
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
        ApplicationKernel::init();

        $class = ChangedClass::class;
        $this->assertWillBeTransformed($class);

        $changedClass = new $class();
        $this->assertSame(
            'Hello World from Code Transformer!',
            $changedClass->test(),
        );

        // Change class
        $classFilePath    = Util::CLASSES_TO_TRANSFORM_DIR . '/ChangedClass.php';
        $classFileContent = Filesystem::readFile($classFilePath);
        $tmpPath          = Util::TMP_DIR . '/ChangedClass.php';
        Filesystem::writeFile($tmpPath, $classFileContent);

        $changedFileContent = str_replace(
            'Hello World!',
            'Hello Changed World!',
            $classFileContent,
        );

        sleep(1);
        Filesystem::writeFile($classFilePath, $changedFileContent);
    }

    public function testCachedChangedClass(): void
    {
        $exception = null;
        try {
            ApplicationKernel::init();

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
        $tmpPath        = Util::TMP_DIR . '/ChangedClass.php';
        if (!file_exists($tmpPath)) {
            return;
        }

        $tmpFileContent = Filesystem::readFile($tmpPath);
        $classFilePath  = Util::CLASSES_TO_TRANSFORM_DIR . '/ChangedClass.php';

        Filesystem::writeFile($classFilePath, $tmpFileContent);

        if ($exception !== null) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw $exception;
        }
    }
}
