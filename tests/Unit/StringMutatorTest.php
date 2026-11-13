<?php

namespace Okapi\CodeTransformer\Tests\Unit;

use InvalidArgumentException;
use Okapi\CodeTransformer\Core\Util\StringMutator;
use PHPUnit\Framework\TestCase;

class StringMutatorTest extends TestCase
{
    public function testEditsUseOriginalPositionsRegardlessOfOrder(): void
    {
        $mutator = new StringMutator('Hello World!');
        $mutator->edit(6, 5, 'PHP')->edit(0, 5, 'Hi')->edit(12, 0, '!');

        static::assertSame('Hi PHP!!', $mutator->getMutatedString());
    }

    public function testNegativeEditLengthIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new StringMutator('Hello World!'))->edit(0, -1, 'Hi');
    }
}
