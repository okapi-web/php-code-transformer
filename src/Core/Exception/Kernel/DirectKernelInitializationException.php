<?php

namespace Okapi\CodeTransformer\Core\Exception\Kernel;

use Okapi\CodeTransformer\Core\Exception\KernelException;

/**
 * # Initialize Kernel Exception
 *
 * This exception is thrown when the CodeTransformerKernel is initialized
 * directly.
 */
class DirectKernelInitializationException extends KernelException
{
    /**
     * DirectKernelInitializationException constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'Cannot initialize CodeTransformerKernel directly. ' .
            'Please extend from CodeTransformerKernel and call the init() method.',
        );
    }
}
