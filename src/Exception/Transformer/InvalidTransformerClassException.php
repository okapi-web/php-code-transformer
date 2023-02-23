<?php

namespace Okapi\CodeTransformer\Exception\Transformer;

use Okapi\CodeTransformer\Exception\TransformerException;

/**
 * # Invalid Transformer Class Exception
 * 
 * This exception is thrown when a transformer class does not extend the
 * `Transformer` class.
 */
class InvalidTransformerClassException extends TransformerException
{
    /**
     * Create a new `TransformerMissingInterfaceException` instance.
     *
     * @param class-string $transformerClass
     */
    public function __construct(string $transformerClass) {
        parent::__construct(
            message: "Transformer class '$transformerClass' does not extend the Transformer class.",
        );
    }
}
