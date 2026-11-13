<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\MultipleTransformer;

use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use ReflectionProperty;

#[RunTestsInSeparateProcesses]
class MultipleTransformersTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testMultipleTransformers(): void
    {
        Util::clearCache();
        Kernel::init();

        $class = TargetClass::class;
        $this->assertWillBeTransformed($class);

        $multipleTransformersClass = new TargetClass();

        $this->assertTransformedMembers($multipleTransformersClass);
    }

    public function testCachedMultipleTransformers(): void
    {
        Kernel::init();

        $class = TargetClass::class;
        $this->assertTransformerLoadedFromCache($class);

        $multipleTransformersClass = new TargetClass();

        $this->assertTransformedMembers($multipleTransformersClass);
    }

    private function assertTransformedMembers(TargetClass $target): void
    {
        $method = new ReflectionMethod($target, 'test');
        $property = new ReflectionProperty($target, 'privateProperty');

        static::assertTrue($method->isPublic());
        static::assertTrue($property->isPublic());
        static::assertSame('Hello from Code Transformer!', $method->invoke($target));
        static::assertSame("You can't get me!", $property->getValue($target));
        static::assertSame(['Hello from Code Transformer!', "You can't get me!"], $target->snapshot());
    }
}
