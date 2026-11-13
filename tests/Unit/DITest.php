<?php

namespace Okapi\CodeTransformer\Tests\Unit;

use LogicException;
use Okapi\CodeTransformer\Core\DI;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;
use stdClass;

#[RunTestsInSeparateProcesses]
class DITest extends TestCase
{
    public function testGetRejectsAnIncompatibleContainerEntry(): void
    {
        DI::getInstance()->register();
        DI::set(stdClass::class, static fn(): string => 'invalid service');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The container must return the requested class.');

        DI::get(stdClass::class);
    }

    public function testMakeRejectsAnIncompatibleContainerEntry(): void
    {
        DI::getInstance()->register();
        DI::set(stdClass::class, static fn(): string => 'invalid service');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('The container must return the requested class.');

        DI::make(stdClass::class);
    }
}
