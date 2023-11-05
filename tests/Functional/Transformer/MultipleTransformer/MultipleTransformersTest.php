<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Kernel\MultipleTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer\Target\MultipleTransformersClass;
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
        MultipleTransformerKernel::init();

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
        MultipleTransformerKernel::init();

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
