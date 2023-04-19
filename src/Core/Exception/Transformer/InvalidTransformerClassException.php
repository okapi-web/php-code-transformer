<?php

namespace Okapi\CodeTransformer\Core\Exception\Transformer;

use Okapi\CodeTransformer\Core\Exception\TransformerException;
use Okapi\CodeTransformer\Transformer;

/**
 * # Invalid Transformer Class Exception
 *
 * This exception is thrown when a transformer class does not extend the
 * {@see Transformer} class.
 */
class InvalidTransformerClassException extends TransformerException
{
    /**
     * InvalidTransformerClassException constructor.
     *
     * @param class-string $transformerClass
     */
    public function __construct(string $transformerClass)
    {
        parent::__construct(
            'Transformer class "' . $transformerClass . '" does not extend the Transformer class.',
        );
    }
}
