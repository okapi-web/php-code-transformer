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
     * Create a new instance of the exception.
     *
     * @param class-string $transformerClass
     */
    public function __construct(string $transformerClass)
    {
        parent::__construct(
            message: "Transformer class '$transformerClass' does not exist.",
        );
    }
}
