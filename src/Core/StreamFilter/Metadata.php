<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\StreamFilter;

use Okapi\CodeTransformer\Core\AutoloadInterceptor\ClassContainer;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Exception\StreamFilter\InvalidStreamException;
use Okapi\CodeTransformer\Transformer\Code;
use Okapi\Path\Path;

/**
 * # Metadata
 *
 * This class is used to store the metadata of the stream filter.
 * It converts {@link stream_get_meta_data()} output into a more convenient
 * format.
 *
 * The public fields mirror PHP stream metadata and remain available to transformers.
 *
 * @mago-expect lint:too-many-properties
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
    /** @var array<array-key, mixed>|null */
    public ?array $crypto;
    public ?string $mediatype;

    /**
     * Metadata constructor.
     *
     * @param mixed  $stream
     * @param string $originalSource
     */
    public function __construct(mixed $stream, string $originalSource)
    {
        if (!is_resource($stream)) {
            // @codeCoverageIgnoreStart
            throw new InvalidStreamException();

            // @codeCoverageIgnoreEnd
        }

        // Create metadata from stream
        /** @var array{timed_out: bool, blocked: bool, eof: bool, unread_bytes: int, stream_type: string, wrapper_type?: string, wrapper_data?: mixed, mode: string, seekable: bool, uri?: string, crypto?: array<array-key, mixed>, mediatype?: string} $metadata */
        $metadata = stream_get_meta_data($stream);
        if (!isset($metadata['uri'])) {
            throw new InvalidStreamException();
        }

        $matches = [];
        if (preg_match('/resource=(.+)$/', $metadata['uri'], $matches)) {
            // Resolve the '/resource=...' uri to a path
            $uri = Path::resolve($matches[1]);
            if (!is_string($uri)) {
                throw new InvalidStreamException();
            }

            $metadata['uri'] = $uri;
        }

        $this->timed_out = $metadata['timed_out'];
        $this->blocked = $metadata['blocked'];
        $this->eof = $metadata['eof'];
        $this->unread_bytes = $metadata['unread_bytes'];
        $this->stream_type = $metadata['stream_type'];
        $this->wrapper_type = $metadata['wrapper_type'] ?? '';
        $this->wrapper_data = $metadata['wrapper_data'] ?? null;
        $this->mode = $metadata['mode'];
        $this->seekable = $metadata['seekable'];
        $this->uri = $metadata['uri'];
        $this->crypto = $metadata['crypto'] ?? null;
        $this->mediatype = $metadata['mediatype'] ?? null;

        $classContainer = DI::get(ClassContainer::class);

        $this->code = DI::make(Code::class, [
            'source' => $originalSource,
            'namespacedClass' => $classContainer->getNamespacedClassByPath($this->uri),
        ]);
    }
}
