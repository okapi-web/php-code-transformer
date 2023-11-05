<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\Environment\Kernel;

use Okapi\CodeTransformer\Core\Options\Environment;

class ProductionEnvironmentKernel extends DevelopmentEnvironmentKernel
{
    protected Environment $environment = Environment::PRODUCTION;
}
