<?php

namespace Okapi\CodeTransformer\Service;

/**
 * # Service Interface
 *
 * This interface is used to define a service.
 */
interface ServiceInterface
{
    /**
     * Register a service.
     *
     * @return void
     */
    public function register(): void;
}
