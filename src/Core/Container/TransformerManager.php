<?php

/** @noinspection PhpPropertyOnlyWrittenInspection */
namespace Okapi\CodeTransformer\Core\Container;

use Closure;
use LogicException;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassException;
use Okapi\CodeTransformer\Core\Exception\Transformer\InvalidTransformerClassNameException;
use Okapi\CodeTransformer\Core\Exception\Transformer\TransformerNotFoundException;
use Okapi\CodeTransformer\Core\ServiceInterface;
use Okapi\CodeTransformer\Transformer;
use ReflectionClass as BaseReflectionClass;
use Throwable;

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
     * @var array<array-key, mixed>
     */
    private array $transformers = [];

    /**
     * The list of transformer containers.
     *
     * @var array<string, TransformerContainer> Key is the transformer file path
     */
    private array $transformerContainers = [];

    /**
     * @var null|Closure(class-string<Transformer>): Transformer
     */
    private ?Closure $dependencyInjectionHandler = null;

    // region Pre-Initialization

    /**
     * Add transformers.
     *
     * @param array<array-key, mixed> $transformers
     *
     * @return void
     */
    public function addTransformers(array $transformers): void
    {
        $this->transformers = array_merge($this->transformers, $transformers);
    }

    // endregion

    // region Initialization

    /**
     * @param null|(Closure(class-string<Transformer>): Transformer) $dependencyInjectionHandler
     */
    public function registerCustomDependencyInjectionHandler(?Closure $dependencyInjectionHandler): void
    {
        $this->dependencyInjectionHandler = $dependencyInjectionHandler;
    }

    public function register(): void
    {
        $this->loadTransformers();
    }

    private function loadTransformers(): void
    {
        foreach (array_keys($this->transformers) as $transformerKey) {
            /** @var mixed $transformer */
            $transformer = $this->transformers[$transformerKey];
            $this->loadTransformer($transformer);
        }
    }

    /**
     * @param mixed $transformerClassName
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function loadTransformer(mixed $transformerClassName): void
    {
        if (!is_string($transformerClassName)) {
            throw new InvalidTransformerClassNameException();
        }
        if (!class_exists($transformerClassName)) {
            throw new TransformerNotFoundException($transformerClassName);
        }
        if (!is_subclass_of($transformerClassName, Transformer::class)) {
            throw new InvalidTransformerClassException($transformerClassName);
        }

        /** @var mixed $transformerInstance */
        $transformerInstance = $this->instantiateTransformer($transformerClassName);

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
        if ($filePath === false) {
            throw new LogicException('A transformer must have a source file.');
        }
        $this->transformerContainers[$filePath] = $transformerContainer;
    }

    /** @param class-string<Transformer> $class */
    private function instantiateTransformer(string $class): mixed
    {
        if ($this->dependencyInjectionHandler !== null) {
            return ($this->dependencyInjectionHandler)($class, ...$this->getAdditionalDependencyInjectionParams());
        }

        try {
            return DI::make($class);
        } catch (Throwable) {
            throw new TransformerNotFoundException($class);
        }
    }

    /** @return list<mixed> */
    protected function getAdditionalDependencyInjectionParams(): array
    {
        return [];
    }

    // endregion

    /**
     * Get the transformers.
     *
     * @return array<array-key, mixed>
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
