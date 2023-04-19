<?php

namespace Okapi\CodeTransformer\Core\StreamFilter;

use Okapi\CodeTransformer\Core\StreamFilter;

/**
 * # Filter Injector
 *
 * This class is responsible for switching the original file path with a
 * {@link https://www.php.net/manual/wrappers.php.php#wrappers.php.filter php://filter stream}.
 *
 * @see StreamFilter::register() - Initialization of the PHP filter.
 * @see StreamFilter::filter() - Transformation of the PHP code.
 * <br>
 * @see https://www.php.net/manual/wrappers.php.php#wrappers.php.filter PHP Stream Filter
 */
class FilterInjector
{
    /**
     * Allows the application to inject filters into the PHP code.
     *
     * @see StreamFilter::filter()
     */
    public const PHP_FILTER_READ = 'php://filter/read=';

    /**
     * Rewrite the PHP code to inject the filter.
     *
     * @param string $filePath
     *
     * @return string
     */
    public function rewrite(string $filePath): string
    {
        // Create a filter for the given file
        return sprintf(
            "%s%s/resource=%s",
            static::PHP_FILTER_READ,
            StreamFilter::FILTER_ID,
            $filePath
        );
    }
}
