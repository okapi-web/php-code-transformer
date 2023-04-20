<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\AddedTransformerClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\AddedTransformerKernel;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class AddedTransformerTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testAddedTransformer(): void
    {
        Util::clearCache();
        ApplicationKernel::init();

        $class = AddedTransformerClass::class;
        $this->assertWillBeTransformed($class);

        $addedTransformerClass = new $class();

        $this->assertSame(
            'Hello Code Transformer!',
            $addedTransformerClass->test(),
        );
    }

    public function testCachedAddedTransformer(): void
    {
        AddedTransformerKernel::init();

        $class = AddedTransformerClass::class;
        $this->assertWillBeTransformed($class);

        $addedTransformerClass = new $class();
        $this->assertSame(
            'Hello from Code Transformer!',
            $addedTransformerClass->test(),
        );
    }
}
