<?php

namespace Okapi\CodeTransformer\Core\Container;

use Okapi\CodeTransformer\Transformer;

// TODO: docs
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
