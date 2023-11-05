<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Kernel\AddedTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Kernel\AddedTransformer\Kernel\BeforeAddedTransformerKernel;
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
        BeforeAddedTransformerKernel::init();

        $class = Target::class;
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

        $class = Target::class;
        $this->assertWillBeTransformed($class);

        $addedTransformerClass = new $class();
        $this->assertSame(
            'Hello from Code Transformer!',
            $addedTransformerClass->test(),
        );
    }
}
