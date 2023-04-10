<?php

namespace Okapi\CodeTransformer;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Exception\Kernel\DirectKernelInitializationException;
use Okapi\CodeTransformer\Service\AutoloadInterceptor;
use Okapi\CodeTransformer\Service\CacheStateManager;
use Okapi\CodeTransformer\Service\DI;
use Okapi\CodeTransformer\Service\Options;
use Okapi\CodeTransformer\Service\StreamFilter;
use Okapi\CodeTransformer\Service\TransformerContainer;
use Okapi\Singleton\Singleton;

/**
 * # Code Transformer Kernel
 *
 * The `CodeTransformerKernel` is the heart of the Code Transformer library.
 * It manages an environment for Code Transformation.
 *
 * 1. Extends this class and define a list of transformers in the
 *    `$transformers` property.
 * 2. Call the `init()` method early in the application lifecycle.
 */
abstract class CodeTransformerKernel
{
    use Singleton;

    // region Settings

    /**
     * The cache directory.
     * <br><b>Default:</b> ROOT_DIR/cache/code-transformer<br>
     *
     * @var string|null
     */
    protected ?string $cacheDir = null;

    /**
     * The cache file mode.
     * <br><b>Default:</b> 0777 & ~{@link umask()}<br>
     *
     * @var int|null
     */
    protected ?int $cacheFileMode = null;

    /**
     * Enable debug mode. This will disable the cache.
     * <br><b>Default:</b> false<br>
     *
     * @var bool
     */
    protected bool $debug = false;

    // endregion

    /**
     * List of transformers to be applied.
     *
     * @var class-string<Transformer>[]
     */
    protected array $transformers = [];

    // region DI

    #[Inject]
    private Options $options;

    #[Inject]
    protected TransformerContainer $transformerContainer;

    #[Inject]
    private CacheStateManager $cacheStateManager;

    #[Inject]
    private StreamFilter $streamFilter;

    #[Inject]
    private AutoloadInterceptor $autoloadInterceptor;

    /**
     * Make the constructor public to allow the DI container to instantiate the class.
     */
    public function __construct() {}

    // endregion

    /**
     * Resolve instance with dependency injection.
     *
     * @inheritDoc
     */
    public static function getInstance(): static
    {
        if (!isset(static::$instance)) {
            static::registerDependencyInjection();

            static::$instance = DI::get(static::class);
        }

        return static::$instance;
    }

    /**
     * Initialize the kernel.
     *
     * @return void
     */
    public static function init(): void
    {
        static::ensureNotKernelNamespace();

        $instance = static::getInstance();
        $instance->ensureNotInitialized();

        // Initialize the services
        $instance->preInit();
        $instance->registerServices();
        $instance->registerAutoloadInterceptor();

        $instance->setInitialized();
    }

    /**
     * Register the dependency injection.
     *
     * @return void
     */
    protected static function registerDependencyInjection(): void
    {
        DI::getInstance()->register();
    }

    /**
     * Pre-initialize the services.
     *
     * @return void
     */
    protected function preInit(): void
    {
        // Set options
        $this->options->setOptions(
            cacheDir:      $this->cacheDir,
            cacheFileMode: $this->cacheFileMode,
            debug:         $this->debug,
        );

        // Add the transformers
        $this->transformerContainer->addTransformers($this->transformers);
    }

    /**
     * Register the services.
     *
     * @return void
     */
    protected function registerServices(): void
    {
        // Options provider
        $this->options->register();

        // Manage the user-defined transformers
        $this->transformerContainer->register();

        // Cache path manager
        $this->cacheStateManager->register();

        // Stream filter -> Source transformer
        $this->streamFilter->register();
    }

    /**
     * Register the autoload interceptor.
     *
     * @return void
     */
    protected function registerAutoloadInterceptor(): void
    {
        // Overload the composer class loaders
        $this->autoloadInterceptor->register();
    }

    /**
     * Make sure that the kernel is not called from this class.
     *
     * @return void
     */
    protected static function ensureNotKernelNamespace(): void
    {
        // Get current namespace and class name
        $namespace = get_called_class();

        if ($namespace === CodeTransformerKernel::class) {
            throw new DirectKernelInitializationException;
        }
    }
}
