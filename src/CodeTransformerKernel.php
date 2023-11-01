<?php

namespace Okapi\CodeTransformer;

use DI\Attribute\Inject;
use Okapi\CodeTransformer\Core\AutoloadInterceptor;
use Okapi\CodeTransformer\Core\Cache\CacheStateManager;
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

    #[Inject]
    private CacheStateManager $cacheStateManager;

    #[Inject]
    private StreamFilter $streamFilter;

    #[Inject]
    private AutoloadInterceptor $autoloadInterceptor;

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
     * @var class-string<Transformer>[]
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

        if ($instance->throwExceptionOnDoubleInitialization) {
            $instance->ensureNotInitialized();
        } elseif ($instance->initialized) {
            return;
        }

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
            environment:   $this->environment,
        );

        // Add the transformers
        $this->transformerManager->addTransformers($this->transformers);
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
        $this->transformerManager->register();

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
