<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Container;

use Error;
use Exception;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Core\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Core\ServiceInterface;
use Okapi\CodeTransformer\Transformer;
use ReflectionClass as BaseReflectionClass;

/**
 * # Transformer Manager
 *
 * This class is used to register and manage the transformers.
 */
class TransformerManager implements ServiceInterface
{
    /**
     * The list of transformer class strings.
     *
     * @var class-string<Transformer>[]
     */
    private array $transformers = [];

    /**
     * The list of transformer containers.
     *
     * @var array<string, TransformerContainer> Key is the transformer file path
     */
    private array $transformerContainers = [];

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
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
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

            // Create transformer container
            $transformerContainer = DI::make(TransformerContainer::class, [
                'transformerInstance' => $transformerInstance,
            ]);

            // Create a reflection of the transformer
            $transformerRefClass = new BaseReflectionClass($transformerInstance);

            $filePath = $transformerRefClass->getFileName();
            $this->transformerContainers[$filePath] = $transformerContainer;
        }
    }

    // endregion

    /**
     * Get the transformers.
     *
     * @return class-string<Transformer>[]
     */
    public function getTransformers(): array
    {
        return $this->transformers;
    }

    /**
     * Get the transformer containers.
     *
     * @return array<string, TransformerContainer> Key is the transformer file path
     */
    public function getTransformerContainers(): array
    {
        return $this->transformerContainers;
    }
}
