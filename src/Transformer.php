<?php

namespace Okapi\CodeTransformer;

use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;

/**
 * # Code Transformer
 *
 * The `CodeTransformer` class provides a foundation for creating a transformer.
 *
 * Transformers extend this class and implement following methods:
 * - `getTargetClass()` - Returns the target class name(s) that this transformer
 *   will be applied to.
 */
abstract class Transformer
{
    /**
     * The order in which the transformer will be applied.
     *
     * @var int
     */
    public int $order = 0;

    /**
     * Get the target class name that this transformer will be applied to.
     *
     * @return class-string|class-string[]
     */
    abstract public function getTargetClass(): string|array;

    /**
     * Transform the source code.
     *
     * @param Code $code
     *
     * @return void
     */
    abstract public function transform(Code $code): void;
}
