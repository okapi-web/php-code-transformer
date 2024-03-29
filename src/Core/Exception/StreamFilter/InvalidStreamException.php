<?php

namespace Okapi\CodeTransformer\Core\Exception\StreamFilter;

use Okapi\CodeTransformer\Core\Exception\StreamFilterException;

/**
 * # Invalid Stream Exception
 *
 * This exception is thrown when the stream is invalid.
 *
 * @codeCoverageIgnore Not sure how to test this
 */
class InvalidStreamException extends StreamFilterException
{
    /**
     * InvalidStreamException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'The stream must be a valid resource.',
        );
    }
}
