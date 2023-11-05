<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass;

use Okapi\CodeTransformer\Core\Exception\Transformer\SyntaxError;
use Okapi\CodeTransformer\Tests\ClassLoaderMockTrait;
use Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass\Kernel\SyntaxErrorInTransformedClassKernel;
use Okapi\CodeTransformer\Tests\Functional\Transformer\SyntaxErrorInTransformedClass\Target\SyntaxErrorClass;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class SyntaxErrorInTransformedClassTest extends TestCase
{
    use ClassLoaderMockTrait;

    public function testSyntaxErrorClass(): void
    {
        Util::clearCache();
        SyntaxErrorInTransformedClassKernel::init();

        $this->expectException(SyntaxError::class);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $syntaxErrorClass = new SyntaxErrorClass();
    }
}
