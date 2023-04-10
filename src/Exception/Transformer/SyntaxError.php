<?php

namespace Okapi\CodeTransformer\Exception\Transformer;

use Microsoft\PhpParser\Diagnostic;
use Okapi\CodeTransformer\Exception\TransformerException;

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
    public function __construct(
        Diagnostic $diagnostic,
        string $code,
        ?SyntaxError $previous = null
    ) {
        parent::__construct(
            "Syntax error in transformed code: $diagnostic->message\n\nFull code:\n```php\n$code\n```",
            previous: $previous,
        );
    }
}
