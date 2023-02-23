<?php

namespace Okapi\CodeTransformer\Service\StreamFilter;

use Okapi\CodeTransformer\Exception\StreamFilter\InvalidStreamException;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata\Code;
use Okapi\Path\Path;

/**
 * # Metadata
 *
 * The `Metadata` class is used to store the metadata of the stream filter.
 * It converts {@link stream_get_meta_data()} output into a more convenient
 * format.
 *
 * @see https://www.php.net/manual/function.stream-get-meta-data.php
 */
class Metadata
{
    /**
     * The source code.
     *
     * @var Code
     */
    public Code $code;

    public bool $timed_out;
    public bool $blocked;
    public bool $eof;
    public int $unread_bytes;
    public string $stream_type;
    public string $wrapper_type;
    public mixed $wrapper_data;
    public string $mode;
    public bool $seekable;
    public string $uri;
    public ?array $crypto;
    public ?string $mediatype;

    /**
     * Metadata constructor.
     *
     * @param mixed  $stream
     * @param string $originalSource
     */
    public function __construct(
        mixed $stream,
        string $originalSource
    ) {
        if (!is_resource($stream)) {
            // @codeCoverageIgnoreStart
            throw new InvalidStreamException;
            // @codeCoverageIgnoreEnd
        }

        $this->code = new Code($originalSource);

        // Create metadata from stream
        $metadata = stream_get_meta_data($stream);
        if (preg_match('/resource=(.+)$/', $metadata['uri'], $matches)) {
            // Resolve the '/resource=...' uri to a path
            $metadata['uri'] = Path::resolve($matches[1]);
        }

        $this->timed_out    = $metadata['timed_out'];
        $this->blocked      = $metadata['blocked'];
        $this->eof          = $metadata['eof'];
        $this->unread_bytes = $metadata['unread_bytes'];
        $this->stream_type  = $metadata['stream_type'];
        $this->wrapper_type = $metadata['wrapper_type'];
        $this->wrapper_data = $metadata['wrapper_data'] ?? null;
        $this->mode         = $metadata['mode'];
        $this->seekable     = $metadata['seekable'];
        $this->uri          = $metadata['uri'];
        $this->crypto       = $metadata['crypto'] ?? null;
        $this->mediatype    = $metadata['mediatype'] ?? null;
    }
}
