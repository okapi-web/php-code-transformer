<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Kernel;

use Okapi\CodeTransformer\CodeTransformerKernel;
use Okapi\CodeTransformer\Core\Options\Environment;
use Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;

class DevelopmentEnvironmentKernel extends CodeTransformerKernel
{
    protected ?string $cacheDir = Util::CACHE_DIR;

    protected Environment $environment = Environment::DEVELOPMENT;

    protected array $transformers = [
        StringTransformer::class,
    ];
}
