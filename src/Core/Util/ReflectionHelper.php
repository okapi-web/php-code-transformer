<?php
/** @noinspection PhpInternalEntityUsedInspection */
namespace Okapi\CodeTransformer\Core\Util;

use Composer\Autoload\ClassLoader;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflector\DefaultReflector;
use Roave\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use Roave\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\ComposerSourceLocator;
use Roave\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;

/**
 * # Reflection Helper
 *
 * This class is used to get reflection class.
 */
class ReflectionHelper
{
    private ClassLoader $classLoader;

    /**
     * Set class loader.
     *
     * @param ClassLoader $classLoader
     *
     * @return void
     */
    public function setClassLoader(ClassLoader $classLoader): void
    {
        $this->classLoader = $classLoader;
    }

    /**
     * Get reflection class.
     *
     * @param class-string $namespacedClass
     *
     * @return ReflectionClass
     */
    public function getReflectionClass(string $namespacedClass): ReflectionClass
    {
        $astLocator = (new BetterReflection())->astLocator();
        $reflector = new DefaultReflector(new AggregateSourceLocator([
            new ComposerSourceLocator($this->classLoader, $astLocator),
            new PhpInternalSourceLocator($astLocator, new ReflectionSourceStubber()),
        ]));

        return $reflector->reflectClass($namespacedClass);
    }
}
