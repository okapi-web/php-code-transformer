<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Service\Matcher;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Service\TransformerContainer;
use Okapi\CodeTransformer\Transformer;
use Okapi\Wildcards\Regex;

class TransformerMatcher
{
    // region DI

    #[Inject]
    private TransformerContainer $transformerContainer;

    // endregion

    /**
     * Cache for the query result of the transformer matching.
     *
     * @var array<class-string, Transformer[]>
     */
    private array $transformerQueryResultCache = [];

    /**
     * Check if the class should be transformed.
     *
     * @param string $namespacedClass
     *
     * @return bool
     */
    public function shouldTransform(string $namespacedClass): bool
    {
        return $this->match($namespacedClass) !== [];
    }

    /**
     * Return the list of transformers that match the given class name.
     *
     * @param string $namespacedClass
     *
     * @return Transformer[]
     */
    public function match(string $namespacedClass): array
    {
        // Check if the query has been cached
        if (isset($this->transformerQueryResultCache[$namespacedClass])) {
            return $this->transformerQueryResultCache[$namespacedClass];
        }

        // Match the transformers
        $matchedInstances = [];
        foreach ($this->transformerContainer->getTransformerTargets() as $classRegex => $instances) {
            $regex = Regex::fromWildcard($classRegex);
            if ($regex->matches($namespacedClass)) {
                // Check if the transformer has already been matched
                $alreadyMatched = array_filter(
                    $matchedInstances,
                    function (Transformer $transformer) use ($instances) {
                        return in_array($transformer, $instances, true);
                    },
                );

                if ($alreadyMatched) {
                    continue;
                }

                $matchedInstances = array_merge($matchedInstances, $instances);
            }
        }

        // Cache the query result
        $this->transformerQueryResultCache[$namespacedClass] = $matchedInstances;

        return $matchedInstances;
    }
}
