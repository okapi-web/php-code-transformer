<?php

namespace Okapi\CodeTransformer\Tests\Stubs\Transformer;

use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\CodeTransformer\Tests\Stubs\ClassesToTransform\DeleteCacheFileClass;
use Okapi\CodeTransformer\Transformer;

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
