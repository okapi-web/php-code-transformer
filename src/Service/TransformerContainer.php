<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Service;

use Error;
use Exception;
use Okapi\CodeTransformer\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Transformer;

/**
 * # Transformer Container
 *
 * The `TransformerContainer` class is used to manage the transformers.
 */
class TransformerContainer implements ServiceInterface
{
    /**
     * The list of transformers.
     *
     * @var class-string<Transformer>[]
     */
    private array $transformers = [];

    /**
     * Associative array of transformer instances by target class name.
     *
     * @var array<string, Transformer[]> The key is a wildcard target class name.
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
    public function addTransformers(array $transformers): void
    {
        $this->transformers = array_merge(
            $this->transformers,
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
    public function register(): void
    {
        $this->loadTransformers();
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
                $transformerInstance = DI::make($transformer);
            } catch (Error|Exception) {
                throw new TransformerNotFoundException($transformer);
            }

            // Validate the transformer
            $isTransformer = $transformerInstance instanceof Transformer;
            if (!$isTransformer) {
                throw new InvalidTransformerClassException($transformer);
            }
            assert($transformerInstance instanceof Transformer);

            /** @var string[] $targets */
            $targets = (array)$transformerInstance->getTargetClass();

            foreach ($targets as $classRegex) {
                $this->transformerTargets[$classRegex][] = $transformerInstance;
            }
        }
    }

    // endregion

    /**
     * Get the transformer targets.
     *
     * @return array<string, Transformer[]> The key is a wildcard target class name.
     */
    public function getTransformerTargets(): array
    {
        return $this->transformerTargets;
    }
}
