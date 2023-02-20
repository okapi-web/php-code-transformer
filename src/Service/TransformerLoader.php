<?php

namespace Okapi\CodeTransformer\Service;

use Okapi\Singleton\Singleton;

/**
 * # Transformer Loader
 *
 * The `TransformerLoader` class is used to load the code transformers.
 */
class TransformerLoader implements ServiceInterface
{
    use Singleton;

    /**
     * The list of transformers.
     *
     * @var class-string<Transformer>[]
     */
    private array $transformers = [];

    /**
     * Add transformers.
     *
     * @param class-string<Transformer>[] $transformers
     *
     * @return void
     */
    public static function addTransformers(array $transformers): void
    {
        $instance = self::getInstance();

        $instance->transformers = array_merge(
            $instance->transformers,
            $transformers,
        );
    }

    /**
     * Register the transformer loader.
     *
     * @return void
     */
    public static function register(): void
    {
        $instance = self::getInstance();
        $instance->ensureNotAlreadyInitialized();

        // TODO: Load the transformers

        $instance->setInitialized();
    }
}
