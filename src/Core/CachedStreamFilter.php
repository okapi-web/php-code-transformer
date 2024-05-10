<?php

namespace Okapi\CodeTransformer\Core;

use Okapi\CodeTransformer\Core\AutoloadInterceptor\ClassContainer;
use Okapi\CodeTransformer\Core\StreamFilter\Metadata;
use Okapi\Filesystem\Filesystem;
use php_user_filter as PhpStreamFilter;

/**
 * # Cached Stream Filter
 *
 * This class is used to register the cached stream filter.
 *
 * Because the PHP debugger has trouble finding the original file, we always
 * rewrite the file path with a PHP stream filter.
 */
class CachedStreamFilter extends PhpStreamFilter implements ServiceInterface
{
    public const CACHED_FILTER_ID = 'okapi.code-transformer.cached';

    private string $data = '';

    public function register(): void
    {
        stream_filter_register(static::CACHED_FILTER_ID, static::class);
    }

    public function filter($in, $out, &$consumed, bool $closing): int
    {
        // Read stream until EOF
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->data .= $bucket->data;
        }

        // If stream is closed, return the cached file
        if ($closing || feof($this->stream)) {
            $consumed = strlen($this->data);

            $metadata = DI::make(Metadata::class, [
                'stream'         => $this->stream,
                'originalSource' => $this->data,
            ]);

            $classContainer = DI::get(ClassContainer::class);
            $cachedFilePath = $classContainer->getCachedFilePath($metadata->uri);

            $source = Filesystem::readFile($cachedFilePath);

            $bucket = stream_bucket_new($this->stream, $source);
            stream_bucket_append($out, $bucket);

            // Pass the (cached) source code to the next filter
            return PSFS_PASS_ON;
        }

        // No data has been consumed
        return PSFS_PASS_ON;
    }
}
