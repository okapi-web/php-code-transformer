<?php

namespace Okapi\CodeTransformer\Service;

use Error;
use Okapi\CodeTransformer\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Service\Cache\CachePaths;
use Okapi\CodeTransformer\Service\Cache\CacheState;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata;
use Okapi\CodeTransformer\Transformer;
use Okapi\Filesystem\Filesystem;
use Okapi\Singleton\Singleton;
use Okapi\Wildcards\Regex;
use ReflectionClass;

/**
 * # Transformer Container
 *
 * The `TransformerContainer` class is used to manage the transformers.
 */
class TransformerContainer implements ServiceInterface
{
    use Singleton;

    /**
     * The list of transformers.
     *
     * @var class-string<Transformer>[]
     */
    private array $transformers = [];

    /**
     * Associative array of transformer instances by target class name.
     *
     * @var array<string, Transformer[]>
     */
    private array $transformerTargets = [];

    // region Pre-Initialization

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
        $instance->ensureNotInitialized();

        $instance->transformers = array_merge(
            $instance->transformers,
            $transformers,
        );
    }

    // endregion

    // region Initialization

    /**
     * Register the transformer container.
     *
     * @return void
     */
    public static function register(): void
    {
        $instance = self::getInstance();
        $instance->ensureNotInitialized();

        $instance->loadTransformers();

        $instance->setInitialized();
    }

    /**
     * Get the transformer instances.
     *
     * @return void
     */
    private function loadTransformers(): void
    {
        foreach ($this->transformers as $transformer) {
            // Instantiate the transformer
            try {
                $transformerInstance = new $transformer();
            } catch (Error) {
                throw new TransformerNotFoundException($transformer);
            }

            // Validate the transformer
            $isTransformer = $transformerInstance instanceof Transformer;
            if (!$isTransformer) {
                throw new InvalidTransformerClassException($transformer);
            }

            /** @var string[] $targets */
            $targets = (array)$transformerInstance->getTargetClass();

            foreach ($targets as $target) {
                $this->transformerTargets[$target][] = $transformerInstance;
            }
        }
    }

    // endregion

    /**
     * Get the list of target classes.
     *
     * @return class-string[]
     */
    public static function getTargetClasses(): array
    {
        $instance = self::getInstance();
        $instance->ensureInitialized();

        return array_keys($instance->transformerTargets);
    }

    /**
     * Return the list of transformers that match the class name.
     *
     * @param string $className
     *
     * @return Transformer[]
     */
    private function matchTransformers(string $className): array
    {
        $matchedInstances = [];

        foreach ($this->transformerTargets as $target => $instances) {
            $regex = Regex::fromWildcard($target);
            if ($regex->matches($className)) {
                $matchedInstances = array_merge($matchedInstances, $instances);
            }
        }

        return $matchedInstances;
    }

    /**
     * Transform the code.
     *
     * @param Metadata $metadata
     *
     * @return void
     *
     * @noinspection PhpMissingReturnTypeInspection For okapi/aop
     */
    public static function transform(Metadata $metadata)
    {
        $instance = self::getInstance();
        $instance->ensureInitialized();

        $fullClassName = $metadata->code->getFullClassName();

        // Process the transformers
        $transformers = $instance->matchTransformers($fullClassName);
        $instance->processTransformers($metadata, $transformers);

        $originalFilePath = $metadata->uri;
        $cacheFilePath    = CachePaths::getTransformedCachePath($originalFilePath);
        $transformed      = $metadata->code->hasChanges();

        // Save the transformed code
        if ($transformed) {
            Filesystem::writeFile(
                $cacheFilePath,
                $metadata->code->getNewSource(),
            );
        }

        // Update the cache state
        $fileModificationTime = $_SERVER['REQUEST_TIME'] ?? time();
        $transformerFilePaths = $instance->getTransformerFilePaths($transformers);
        $cacheState           = new CacheState(
            originalFilePath:     $originalFilePath,
            cachedFilePath:       $transformed ? $cacheFilePath : null,
            transformedTime:      $fileModificationTime,
            transformerFilePaths: $transformerFilePaths,
        );
        CacheStateManager::setCacheState($originalFilePath, $cacheState);
    }

    /**
     * Get the list of transformer file paths.
     *
     * @param Transformer[] $transformers
     *
     * @return string[]
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getTransformerFilePaths(array $transformers): array
    {
        return array_map(
            function (Transformer $transformer) {
                /** @noinspection PhpUnhandledExceptionInspection Handled by TransformerNotFoundException */
                $reflection = new ReflectionClass($transformer);
                return $reflection->getFileName();
            },
            $transformers,
        );
    }

    /**
     * Process the transformers.
     *
     * @param Metadata      $metadata
     * @param Transformer[] $transformers
     *
     * @return void
     * @noinspection PhpMissingReturnTypeInspection For okapi/aop
     */
    protected function processTransformers(Metadata $metadata, array $transformers)
    {
        // Sort the transformers by priority
        usort(
            $transformers,
            function (Transformer $a, Transformer $b) {
                return $a->order <=> $b->order;
            },
        );

        foreach ($transformers as $transformer) {
            $transformer->transform($metadata->code);
        }
    }
}
