<?php

namespace Okapi\CodeTransformer\Core;

use DI\Container;
use DI\ContainerBuilder;
use Okapi\Singleton\Singleton;

/**
 * # Dependency Injection
 *
 * This class is used to manage the dependency injection container.
 */
class DI implements ServiceInterface
{
    use Singleton;

    /**
     * Dependency injection container.
     *
     * @var Container
     */
    private Container $container;

    /**
     * Dependency injection container builder.
     *
     * @var ContainerBuilder
     */
    private ContainerBuilder $containerBuilder;

    /**
     * Reg
     *
     * @return void
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function register(): void
    {
        $this->ensureNotInitialized();

        $containerBuilder = $this->getContainerBuilder();
        $containerBuilder->useAttributes(true);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->container = $containerBuilder->build();

        $this->setInitialized();
    }

    /**
     * Get the dependency injection container builder.
     *
     * @return ContainerBuilder
     */
    public static function getContainerBuilder(): ContainerBuilder
    {
        $instance = static::getInstance();
        $instance->ensureNotInitialized();

        if (!isset($instance->containerBuilder)) {
            $instance->containerBuilder = new ContainerBuilder();
        }

        return $instance->containerBuilder;
    }

    /**
     * Get an (already initialized) instance of the given class.
     *
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function get(string $class)
    {
        $instance = static::getInstance();
        $instance->ensureInitialized();

        /** @noinspection PhpUnhandledExceptionInspection */
        return $instance->container->get($class);
    }

    /**
     * Replace an existing instance of the given class.
     *
     * @param string $class
     * @param        $value
     *
     * @return void
     */
    public static function set(string $class, $value): void
    {
        $instance = static::getInstance();
        $instance->ensureInitialized();

        $instance->container->set($class, $value);
    }

    /**
     * Create an instance of the given class.
     *
     * This method behaves like a factory.
     *
     * @template T
     *
     * @param class-string<T> $class
     * @param array           $parameters
     *
     * @return T
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public static function make(string $class, array $parameters = [])
    {
        $instance = static::getInstance();
        $instance->ensureInitialized();

        /** @noinspection PhpUnhandledExceptionInspection */
        return $instance->container->make($class, $parameters);
    }
}
