<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass;

use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassNameException;
use Okapi\CodeTransformer\Core\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass\Kernel\InvalidTransformerTypeKernel;
use Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass\Kernel\TransformerDoesNotExistKernel;
use Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass\Kernel\TransformerDoesNotExtendTransformerKernel;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class InvalidTransformerClassTest extends TestCase
{
    /**
     * @see TransformerDoesNotExistKernel
     */
    public function testTransformerNotFound(): void
    {
        Util::clearCache();

        $this->expectException(TransformerNotFoundException::class);

        TransformerDoesNotExistKernel::init();
    }

    /**
     * @see InvalidTransformerTypeKernel
     */
    public function testInvalidTransformerType(): void
    {
        Util::clearCache();

        $this->expectException(InvalidTransformerClassNameException::class);

        InvalidTransformerTypeKernel::init();
    }

    /**
     * @see TransformerDoesNotExtendTransformerKernel
     */
    public function testTransformerDoesNotExtendTransformer(): void
    {
        Util::clearCache();

        $this->expectException(InvalidTransformerClassException::class);

        TransformerDoesNotExtendTransformerKernel::init();
    }
}
