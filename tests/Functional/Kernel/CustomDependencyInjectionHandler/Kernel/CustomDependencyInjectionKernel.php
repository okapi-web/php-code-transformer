<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\CustomDependencyInjectionHandler\Kernel;

use Closure;
use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;

class CustomDependencyInjectionKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected function dependencyInjectionHandler(): ?Closure
    {
        return function (string $transformerClass) {
            echo 'Generating transformer instance: ' . $transformerClass . PHP_EOL;

            return new $transformerClass();
        };
    }

    protected array $transformers = [
        StringTransformer::class,
    ];
}
