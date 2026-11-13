<?php

namespace Okapi\CodeTransformer\Core\Exception\Transformer;

use Microsoft\PhpParser\Diagnostic;
use Okapi\CodeTransformer\Core\Exception\TransformerException;

/**
 * # Syntax Error
 *
 * This exception is thrown when the transformed code contains a syntax error.
 */
class SyntaxError extends TransformerException
{
    /**
     * SyntaxError constructor.
     *
     * @param Diagnostic       $diagnostic
     * @param string           $code
     * @param SyntaxError|null $previous
     */
    public function __construct(Diagnostic $diagnostic, string $code, ?SyntaxError $previous = null)
    {
        $message = <<<MESSAGE
            Syntax error in transformed code: {$diagnostic->message}

            Full code:
            ```php
            {$code}
            ```
            MESSAGE;

        parent::__construct(message: $message, previous: $previous);
    }
}
