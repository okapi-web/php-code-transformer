<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Service\Processor;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Service\Cache\CachePaths;
use Okapi\CodeTransformer\Service\Cache\CacheState;
use Okapi\CodeTransformer\Service\CacheStateManager;
use Okapi\CodeTransformer\Service\DI;
use Okapi\CodeTransformer\Service\Matcher\TransformerMatcher;
use Okapi\CodeTransformer\Service\StreamFilter\Metadata;
use Okapi\CodeTransformer\Transformer;
use Okapi\Filesystem\Filesystem;
use ReflectionClass;

/**
 * # Transformer Processor
 *
 * The `TransformerProcessor` class is used to process the transformers.
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
        $className = $metadata->code->getClassName();

        // Process the transformers
        $transformers = $this->transformerMatcher->match($className);
        $this->processTransformers($metadata, $transformers);

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
        $fileModificationTime = $_SERVER['REQUEST_TIME'] ?? time();
        $transformerFilePaths = $this->getTransformerFilePaths($transformers);
        $cacheState           = DI::make(CacheState::class, [
            'originalFilePath'     => $originalFilePath,
            'className'            => $className,
            'cachedFilePath'       => $transformed ? $cacheFilePath : null,
            'transformedTime'      => $fileModificationTime,
            'transformerFilePaths' => $transformerFilePaths,
        ]);
        $this->cacheStateManager->setCacheState($originalFilePath, $cacheState);
    }

    /**
     * Process the transformers.
     *
     * @param Metadata      $metadata
     * @param Transformer[] $transformers
     *
     * @return void
     */
    private function processTransformers(
        Metadata $metadata,
        array $transformers
    ): void {
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

    /**
     * Get the file paths of the given transformers.
     *
     * @param Transformer[] $transformers
     *
     * @return string[]
     *
     * @noinspection PhpDocMissingThrowsInspection Handled by TransformerNotFoundException
     */
    protected function getTransformerFilePaths(array $transformers): array
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
}
