<?php

namespace Okapi\CodeTransformer\Core\Options;

/**
 * # Environment
 *
 * The environment in which the application is running.
 */
enum Environment
{
    /**
     * Cache will not be checked for updates (better performance).
     */
    case PRODUCTION;

    /**
     * Cache will be checked for updates (better development experience).
     */
    case DEVELOPMENT;
}
