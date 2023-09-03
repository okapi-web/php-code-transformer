<?php
/** @noinspection PhpUnhandledExceptionInspection */
namespace Okapi\CodeTransformer\Tests\Stubs\Kernel;

use Exception;

class ExceptionOnDoubleInitializationKernel extends EmptyKernel
{
    private int $initCount = 0;

    protected bool $throwExceptionOnDoubleInitialization = true;

    protected function preInit(): void
    {
        $this->initCount++;

        if ($this->initCount > 1) {
            throw new Exception('I should not be initialized twice!');
        }

        parent::preInit();
    }
}
