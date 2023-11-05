<?php

namespace Okapi\CodeTransformer\Tests;

use Okapi\Filesystem\Filesystem;

class Util
{
    private const TESTS_DIR                   = __DIR__;

    public const CACHE_DIR                    = self::TESTS_DIR . '/cache';
    private const CACHE_DIR_TRANSFORMED_TESTS = self::CACHE_DIR . '/transformed/tests';

    public const TMP_DIR                         = __DIR__ . '/tmp';

    public const CACHE_STATES_FILE = self::CACHE_DIR . '/cache_states.php';

    public static function clearCache(): void
    {
        Filesystem::rm(
            path: Util::CACHE_DIR,
            recursive: true,
            force: true,
        );
        Filesystem::rm(
            path: Util::TMP_DIR,
            recursive: true,
            force: true,
        );
    }

    public static function getFilePath(string $class): string
    {
        $class = str_replace(
            search: 'Okapi\CodeTransformer\Tests\\',
            replace: '',
            subject: $class,
        );

        $class = str_replace(
            search: '\\',
            replace: '/',
            subject: $class,
        );

        return Util::TESTS_DIR . '/' . $class . '.php';
    }

    public static function getTransformedFilePath(string $class): string
    {
        $class = str_replace(
            search: 'Okapi\CodeTransformer\Tests\\',
            replace: '',
            subject: $class,
        );

        $class = str_replace(
            search: '\\',
            replace: '/',
            subject: $class,
        );

        return Util::CACHE_DIR_TRANSFORMED_TESTS . '/' . $class . '.php';
    }
}
