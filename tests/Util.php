<?php

namespace Okapi\CodeTransformer\Tests;

use Okapi\Filesystem\Filesystem;

class Util
{
    public const CACHE_DIR                       = __DIR__ . '/cache';
    public const STUBS_DIR                       = __DIR__ . '/Stubs';
    public const CACHED_STUBS_DIR                = self::CACHE_DIR . '/transformed/tests/Stubs';
    public const CLASSES_TO_TRANSFORM_DIR        = self::STUBS_DIR . '/ClassesToTransform';
    public const CACHED_CLASSES_TO_TRANSFORM_DIR = self::CACHED_STUBS_DIR . '/ClassesToTransform';
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
}
