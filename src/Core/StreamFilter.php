<?php

namespace Okapi\CodeTransformer\Core;

use Okapi\CodeTransformer\Core\Processor\TransformerProcessor;
use Okapi\CodeTransformer\Core\StreamFilter\Metadata;
use php_user_filter as PhpStreamFilter;

/**
 * # Stream Filter
 *
 * This class is used to register the stream filter.
 */
class StreamFilter extends PhpStreamFilter implements ServiceInterface
{
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
    public function register(): void
    {
        // Register the stream filter
        stream_filter_register(static::FILTER_ID, static::class);
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
            $metadata = DI::make(Metadata::class, [
                'stream'         => $this->stream,
                'originalSource' => $this->data,
            ]);

            // Transform the code
            $transformerProcessor = DI::get(TransformerProcessor::class);
            $transformerProcessor->transform($metadata);

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
