<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Processor;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Core\Cache\CachePaths;
use Okapi\CodeTransformer\Core\Cache\CacheState\NoTransformationsCacheState;
use Okapi\CodeTransformer\Core\Cache\CacheState\TransformedCacheState;
use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
use Okapi\CodeTransformer\Core\Container\TransformerContainer;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Matcher\TransformerMatcher;
use Okapi\CodeTransformer\Core\StreamFilter\Metadata;
use Okapi\Filesystem\Filesystem;
use ReflectionClass as BaseReflectionClass;

/**
 * # Transformer Processor
 *
 * This class is used to process the transformers.
 */
class TransformerProcessor
{
    // region DI

    #[Inject]
    protected TransformerMatcher $transformerMatcher;

    #[Inject]
    protected CachePaths $cachePaths;

    #[Inject]
    protected CacheStateManager $cacheStateManager;

    // endregion

    /**
     * Transform the code.
     *
     * @param Metadata $metadata
     *
     * @return void
     */
    public function transform(Metadata $metadata): void
    {
        $namespacedClass = $metadata->code->getNamespacedClass();

        // Process the transformers
        $transformerContainers = $this->transformerMatcher->getMatchedTransformerContainers($namespacedClass);
        $this->processTransformers($metadata, $transformerContainers);

        $originalFilePath = $metadata->uri;
        $cacheFilePath    = $this->cachePaths->getTransformedCachePath($originalFilePath);
        $transformed      = $metadata->code->hasChanges();

        // Save the transformed code
        if ($transformed) {
            Filesystem::writeFile(
                $cacheFilePath,
                $metadata->code->getNewSource(),
            );
        }

        // Update the cache state
        $modificationTime = $_SERVER['REQUEST_TIME'] ?? time();
        if ($transformed) {
            $transformerFilePaths = $this->getTransformerFilePaths($transformerContainers);

            $cacheState = DI::make(TransformedCacheState::class, [
                'data' => [
                    'originalFilePath'     => $originalFilePath,
                    'modificationTime'     => $modificationTime,
                    'transformedFilePath'  => $cacheFilePath,
                    'transformerFilePaths' => $transformerFilePaths,
                ],
            ]);
        } else {
            $cacheState = DI::make(NoTransformationsCacheState::class, [
                'data' => [
                    'originalFilePath' => $originalFilePath,
                    'modificationTime' => $modificationTime,
                ],
            ]);
        }

        $this->cacheStateManager->setCacheState($originalFilePath, $cacheState);
    }

    /**
     * Process the transformers.
     *
     * @param Metadata               $metadata
     * @param TransformerContainer[] $transformerContainers
     *
     * @return void
     */
    protected function processTransformers(
        Metadata $metadata,
        array    $transformerContainers,
    ): void {
        // Sort the transformers by priority
        usort(
            $transformerContainers,
            function (TransformerContainer $a, TransformerContainer $b) {
                return $a->transformerInstance->order <=> $b->transformerInstance->order;
            },
        );

        foreach ($transformerContainers as $transformerContainer) {
            $transformerContainer->transformerInstance->transform(
                $metadata->code,
            );
        }
    }

    /**
     * Get the file paths of the given transformers.
     *
     * @param TransformerContainer[] $transformerContainers
     *
     * @return string[]
     *
     * @noinspection PhpDocMissingThrowsInspection Handled by TransformerNotFoundException
     *
     * @todo: Move this logic to the CacheStateManager by checking
     *   if the transformers exist and are up-to-date.
     */
    protected function getTransformerFilePaths(
        array $transformerContainers
    ): array {
        return array_map(
            function (TransformerContainer $transformerContainer) {
                /** @noinspection PhpUnhandledExceptionInspection Handled by TransformerNotFoundException */
                $reflection = new BaseReflectionClass(
                    $transformerContainer->transformerInstance
                );
                return $reflection->getFileName();
            },
            $transformerContainers,
        );
    }
}
