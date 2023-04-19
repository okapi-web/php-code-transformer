<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Matcher;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Core\Cache\CacheState\EmptyResultCacheState;
use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
use Okapi\CodeTransformer\Core\Container\TransformerContainer;
use Okapi\CodeTransformer\Core\Container\TransformerManager;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Transformer;
use Okapi\Wildcards\Regex;

/**
 * # Transformer Matcher
 *
 * This class is used to match the transformers to the classes.
 */
class TransformerMatcher
{
    // region DI

    #[Inject]
    private TransformerManager $transformerContainer;

    #[Inject]
    private CacheStateManager $cacheStateManager;

    // endregion

    /**
     * Cache for the query result of the transformer matching.
     *
     * @var array<class-string, Transformer[]>
     */
    private array $matchedTransformerContainers = [];

    /**
     * Check if the class should be transformed.
     *
     * @param string $namespacedClass
     * @param string $filePath
     *
     * @return bool
     */
    public function match(string $namespacedClass, string $filePath): bool
    {
        // Get the transformers
        $transformerContainers = $this->transformerContainer->getTransformerContainers();

        // Match the transformers
        $matchedTransformerContainers = [];
        foreach ($transformerContainers as $transformerContainer) {
            $wildcardPatterns = (array)$transformerContainer->transformerInstance->getTargetClass();

            foreach ($wildcardPatterns as $wildcardPattern) {
                $regex = Regex::fromWildcard($wildcardPattern);
                if ($regex->matches($namespacedClass)) {
                    // Check if the transformer has already been matched
                    $alreadyMatched = array_reduce(
                        $matchedTransformerContainers,
                        function (bool $carry, TransformerContainer $container) use ($transformerContainer) {
                            return $carry || $container === $transformerContainer;
                        },
                        false,
                    );

                    if ($alreadyMatched) {
                        continue;
                    }

                    $matchedTransformerContainers[] = $transformerContainer;
                }
            }
        }

        // Cache the result
        $this->matchedTransformerContainers[$namespacedClass] = $matchedTransformerContainers;

        // Cache the result
        if (!$matchedTransformerContainers) {
            $cacheState = DI::make(EmptyResultCacheState::class, [
                'data' => [
                    'originalFilePath' => $filePath,
                    'modificationTime' => filemtime($filePath),
                ],
            ]);

            // Set the cache state
            $this->cacheStateManager->setCacheState(
                $filePath,
                $cacheState,
            );
        }

        return (bool)$matchedTransformerContainers;
    }

    /**
     * Get the matched transformers for the given class.
     *
     * @param string $namespacedClass
     *
     * @return TransformerContainer[]
     */
    public function getMatchedTransformerContainers(string $namespacedClass): array
    {
        // Check if the query has been cached
        return $this->matchedTransformerContainers[$namespacedClass] ?? [];
    }
}
