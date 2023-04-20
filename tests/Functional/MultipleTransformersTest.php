<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\MultipleTransformersClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class MultipleTransformersTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testMultipleTransformers(): void
    {
        Util::clearCache();
        ApplicationKernel::init();

        $class = MultipleTransformersClass::class;
        $this->assertWillBeTransformed($class);

        $multipleTransformersClass = new $class();

        $this->assertSame(
            'Hello from Code Transformer!',
            $multipleTransformersClass->test(),
        );

        $this->assertSame(
            "You can't get me!",
            $multipleTransformersClass->privateProperty,
        );
    }

    public function testCachedMultipleTransformers(): void
    {
        ApplicationKernel::init();

        $class = MultipleTransformersClass::class;
        $this->assertTransformerLoadedFromCache($class);

        $multipleTransformersClass = new $class();

        $this->assertSame(
            'Hello from Code Transformer!',
            $multipleTransformersClass->test(),
        );

        $this->assertSame(
            "You can't get me!",
            $multipleTransformersClass->privateProperty,
        );
    }
}
