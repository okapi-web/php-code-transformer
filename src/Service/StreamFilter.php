<?php

namespace Okapi\CodeTransformer\Service;

use Okapi\CodeTransformer\Service\StreamFilter\Metadata;
use Okapi\Singleton\Singleton;
use php_user_filter as PhpStreamFilter;

/**
 * # Stream Filter
 *
 * The `StreamFilter` class is used to register the stream filter.
 */
class StreamFilter extends PhpStreamFilter implements ServiceInterface
{
    use Singleton;

    /**
     * Filter ID.
     */
    public const FILTER_ID = 'okapi.code-transformer';

    /**
     * String buffer.
     *
     * @var string
     */
    private string $data = '';

    /**
     * Register the source transformer.
     *
     * @return void
     */
    public static function register(): void
    {
        $instance = self::getInstance();
        $instance->ensureNotInitialized();

        // Register the stream filter
        stream_filter_register(self::FILTER_ID, self::class);

        $instance->setInitialized();
    }

    /**
     * This method is called when the stream is read.
     *
     * @param      $in
     * @param      $out
     * @param      $consumed
     * @param bool $closing
     *
     * @return int
     *
     * @see https://www.php.net/manual/php-user-filter.filter.php
     */
    public function filter($in, $out, &$consumed, bool $closing): int
    {
        // Read the stream until EOF
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->data .= $bucket->data;
        }

        // If the stream is closed, transform the code
        if ($closing || feof($this->stream)) {
            $consumed = strlen($this->data);

            // Store the metadata
            $metadata = new Metadata($this->stream, $this->data);

            // Transform the code
            TransformerContainer::transform($metadata);

            // Set the new source code
            $source = $metadata->code->getNewSource();

            $bucket = stream_bucket_new($this->stream, $source);
            stream_bucket_append($out, $bucket);

            // Pass the (transformed) source code to the next filter
            return PSFS_PASS_ON;
        }

        // No data has been consumed
        return PSFS_FEED_ME;
    }
}
