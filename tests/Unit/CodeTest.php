<?php

namespace Okapi\CodeTransformer\Tests\Unit;

use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Transformer\Code;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use stdClass;

class CodeTest extends TestCase
{
    #[RunInSeparateProcess]
    public function testClassNamesWithAndWithoutNamespace(): void
    {
        DI::getInstance()->register();

        static::assertSame('stdClass', (new Code('<?php', stdClass::class))->getClassName());
        static::assertSame('CodeTest', (new Code('<?php', self::class))->getClassName());
    }
}
