<?php

namespace Okapi\CodeTransformer\Core\Exception\Transformer;

use Okapi\CodeTransformer\Core\Exception\TransformerException;

class InvalidTransformerClassNameException extends TransformerException
{
    public function __construct()
    {
        parent::__construct(
            'Transformer class name in Kernel must be a string.',
        );
    }
}
