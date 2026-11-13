<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\CustomDependencyInjectionHandler;

use Closure;
use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;

class Kernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected function dependencyInjectionHandler(): ?Closure
    {
        /** @param class-string<\Okapi\CodeTransformer\Transformer> $transformerClass */
        return static function (string $transformerClass): \Okapi\CodeTransformer\Transformer {
            echo 'Generating transformer instance: ' . $transformerClass . PHP_EOL;

            return new StringTransformer();
        };
    }

    protected array $transformers = [
        StringTransformer::class,
    ];
}
