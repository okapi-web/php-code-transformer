<?php

namespace Okapi\CodeTransformer;

use Okapi\CodeTransformer\Transformer\Code;

/**
 * # Code Transformer
 *
 * This class provides a foundation for creating a transformer.
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
     * Wildcards are supported. See Okapi/Wildcards for more information.
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
