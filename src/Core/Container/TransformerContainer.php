<?php

namespace Okapi\CodeTransformer\Core\Container;

use Okapi\CodeTransformer\Transformer;

/**
 * # Transformer Container
 *
 * This class is used to store the transformer instances.
 */
class TransformerContainer
{
    /**
     * TransformerContainer constructor.
     *
     * @param Transformer $transformerInstance
     */
    public function __construct(
        public Transformer $transformerInstance,
    ) {}
}
