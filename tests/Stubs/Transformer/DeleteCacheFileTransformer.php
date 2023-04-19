<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\DeleteCacheFileClass;
use Okapi\CodeTransformer\Transformer;
use Okapi\CodeTransformer\Transformer\Code;

class DeleteCacheFileTransformer extends Transformer
{
    public function getTargetClass(): string|array
    {
        return DeleteCacheFileClass::class;
    }

    public function transform(Code $code): void
    {
        $code->append('// Hello from Code Transformer!');
    }
}
