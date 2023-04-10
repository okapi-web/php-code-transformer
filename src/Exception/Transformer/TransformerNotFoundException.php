<?php

namespace Okapi\CodeTransformer\Exception\Transformer;

use Okapi\CodeTransformer\Exception\TransformerException;

/**
 * # Transformer Not Found Exception
 *
 * This exception is thrown when an invalid transformer class is specified.
 */
class TransformerNotFoundException extends TransformerException
{
    /**
     * TransformerNotFoundException constructor.
     *
     * @param class-string $transformerClass
     */
    public function __construct(string $transformerClass)
    {
        parent::__construct(
            "Transformer class '$transformerClass' does not exist.",
        );
    }
}
