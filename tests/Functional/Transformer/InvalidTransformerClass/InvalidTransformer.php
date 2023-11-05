<?php

namespace Okapi\CodeTransformer\Tests\Functional\Transformer\InvalidTransformerClass;

use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Target\StringClass;
use Okapi\CodeTransformer\Transformer\Code;

class InvalidTransformer
{
    public function getTargetClass(): string|array
    {
        return StringClass::class;
    }

    public function transform(Code $code): void
    {
        // Do nothing
    }
}
