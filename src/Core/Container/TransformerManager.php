<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Container;

use Closure;
use Error;
use Exception;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassNameException;
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

    /**
     * @var ?Closure(class-string<Transformer>): Transformer
     */
    private ?Closure $dependencyInjectionHandler = null;

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
     * @param null|(Closure(class-string<Transformer>): Transformer) $dependencyInjectionHandler
     */
    public function registerCustomDependencyInjectionHandler(
        ?Closure $dependencyInjectionHandler
    ): void {
        $this->dependencyInjectionHandler = $dependencyInjectionHandler;
    }

    public function register(): void
    {
        $this->loadTransformers();
    }

    private function loadTransformers(): void
    {
        foreach ($this->transformers as $transformer) {
            $this->loadTransformer($transformer);
        }
    }

    /**
     * @param class-string<Transformer> $transformerClassName
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function loadTransformer(mixed $transformerClassName): void
    {
        // Check if the transformer is already loaded
        if (array_key_exists($transformerClassName, $this->transformerContainers)) {
            return;
        }

        // Validate the transformer
        if (gettype($transformerClassName) !== 'string') {
            throw new InvalidTransformerClassNameException;
        }

        // Instantiate the transformer
        if ($this->dependencyInjectionHandler) {
            $transformerInstance = ($this->dependencyInjectionHandler)($transformerClassName);

            if (!($transformerInstance instanceof Transformer)) {
                throw new InvalidTransformerClassException($transformerClassName);
            }
        } else {
            try {
                $transformerInstance = DI::make($transformerClassName);
            } catch (Error|Exception) {
                throw new TransformerNotFoundException($transformerClassName);
            }
        }

        // Validate the transformer
        $isTransformer = $transformerInstance instanceof Transformer;
        if (!$isTransformer) {
            throw new InvalidTransformerClassException($transformerClassName);
        }

        // Create transformer container
        $transformerContainer = DI::make(TransformerContainer::class, [
            'transformerInstance' => $transformerInstance,
        ]);

        // Create a reflection of the transformer
        $transformerRefClass = new BaseReflectionClass($transformerInstance);

        $filePath = $transformerRefClass->getFileName();
        $this->transformerContainers[$filePath] = $transformerContainer;
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
