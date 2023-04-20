<?php

namespace Okapi\CodeTransformer\Tests\Functional;

use Okapi\CodeTransformer\Core\Exception\Transformer\SyntaxError;
use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\SyntaxErrorClass;
use Okapi\CodeTransformer\Tests\Stubs\Kernel\ApplicationKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class SyntaxErrorClassTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testSyntaxErrorClass(): void
    {
        Util::clearCache();
        ApplicationKernel::init();

        $this->expectException(SyntaxError::class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $syntaxErrorClass = new SyntaxErrorClass();
    }
}
