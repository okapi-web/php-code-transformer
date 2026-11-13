<?php

namespace Okapi\CodeTransformer;

use Closure;
use DI\Attribute\Inject;
use LogicException;
use Okapi\CodeTransformer\Core\AutoloadInterceptor;
use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
use Okapi\CodeTransformer\Core\CachedStreamFilter;
use Okapi\CodeTransformer\Core\Container\TransformerManager;
use Okapi\CodeTransformer\Core\DI;
use Okapi\CodeTransformer\Core\Exception\Kernel\DirectKernelInitializationException;
use Okapi\CodeTransformer\Core\Options;
use Okapi\CodeTransformer\Core\Options\Environment;
use Okapi\CodeTransformer\Core\StreamFilter;
use Okapi\Singleton\Singleton;

/**
 * # Code Transformer Kernel
 *
 * This class is the heart of the Code Transformer library.
 * It manages an environment for Code Transformation.
 *
 * 1. Extend this class and define a list of transformers in the
 *    {@link $transformers} property.
 * 2. Call the {@link init()} method early in the application lifecycle.
 *
 * If you want to modify the kernel options dynamically, override the
 * {@link configureOptions()} method.
 */
abstract class CodeTransformerKernel
{
    use Singleton;

    // region DI

    #[Inject]
    private Options $options;

    /** @internal */
    #[Inject]
    protected TransformerManager $transformerManager;

    /**
     * Make the constructor public to allow the DI container to instantiate the
     * class.
     *
     * @internal
     */
    public function __construct() {}

    // endregion

    // region Settings

    /**
     * The cache directory.
     * <br><b>Default:</b> ROOT_DIR/cache/code-transformer<br>
     *
     * @var string|null
     */
    protected ?string $cacheDir = null;

    /**
     * The exclude paths. Paths/directories in this array will be excluded
     *
     * @var string[]
     */
    protected array $excludePaths = [];

    /**
     * The cache file mode.
     * <br><b>Default:</b> 0777 & ~{@link umask()}<br>
     *
     * @var int|null
     */
    protected ?int $cacheFileMode = null;

    /**
     * Enable debug mode. This will disable the cache.
     * <br><b>Default:</b> {@link false}<br>
     *
     * @var bool
     */
    protected bool $debug = false;

    /**
     * The environment in which the application is running.
     * <br><b>Default:</b> {@link Environment::DEVELOPMENT}<br><br>
     *
     * If {@link Environment::PRODUCTION}, the cache will not be checked for
     *   updates (better performance).<br>
     * If {@link Environment::DEVELOPMENT}, the cache will be checked for
     *   updates (better development experience).
     *
     * @var Environment
     */
    protected Environment $environment = Environment::DEVELOPMENT;

    /**
     * Throw an exception if the kernel is initialized twice.
     * <br><b>Default:</b> {@link false}<br>
     *
     * If {@link false}, any subsequent call to {@link init()} will be
     *  ignored.
     *
     * @var bool
     */
    protected bool $throwExceptionOnDoubleInitialization = false;

    // endregion

    /**
     * List of transformers to be applied.
     *
     * @var array<array-key, mixed>
     */
    protected array $transformers = [];

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

        /** @var mixed $instance */
        $instance = static::$instance;
        if (!$instance instanceof static) {
            throw new LogicException('The container must return the requested kernel class.');
        }

        return $instance;
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

        if ($instance->throwExceptionOnDoubleInitialization) {
            $instance->ensureNotInitialized();
        }
        if (!$instance->throwExceptionOnDoubleInitialization && $instance->initialized) {
            return;
        }

        // Initialize the services
        $instance->preInit();
        $instance->configureOptions();
        $instance->registerServices();
        $instance->registerAutoloadInterceptor();

        $instance->setInitialized();
    }

    /**
     * Internal dependency injection.
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
            cacheDir: $this->cacheDir,
            cacheFileMode: $this->cacheFileMode,
            debug: $this->debug,
            environment: $this->environment,
            excludePaths: $this->excludePaths,
        );

        // Add the transformers
        $this->transformerManager->addTransformers($this->transformers);
    }

    /**
     * Configure or modify kernel options.
     *
     * @return void
     */
    protected function configureOptions(): void
    {
        // Override this method to configure the options dynamically
    }

    /**
     * Custom dependency injection handler.
     *
     * Pass a closure that takes a transformer class name as the argument and
     * returns a transformer instance.
     *
     * @return null|Closure(class-string<Transformer>): Transformer
     */
    protected function dependencyInjectionHandler(): ?Closure
    {
        // Override this method to configure the dependency injection handler

        return null;
    }

    protected function registerServices(): void
    {
        $this->options->register();

        $this->transformerManager->registerCustomDependencyInjectionHandler($this->dependencyInjectionHandler());
        $this->transformerManager->register();

        DI::get(CacheStateManager::class)->register();

        DI::get(StreamFilter::class)->register();
        DI::get(CachedStreamFilter::class)->register();
    }

    /**
     * Register the autoload interceptor.
     *
     * @return void
     */
    protected function registerAutoloadInterceptor(): void
    {
        // Overload the composer class loaders
        DI::get(AutoloadInterceptor::class)->register();
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
            throw new DirectKernelInitializationException();
        }
    }
}
