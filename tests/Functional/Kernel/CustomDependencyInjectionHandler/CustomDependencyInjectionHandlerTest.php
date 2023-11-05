<?php

namespace Okapi\CodeTransformer\Tests\Functional\Kernel\CustomDependencyInjectionHandler;

use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Target\StringClass;
use Okapi\CodeTransformer\Tests\Functional\Cache\CachedFileAndDestructor\Transformer\StringTransformer;
use Okapi\CodeTransformer\Tests\Util;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
class CustomDependencyInjectionHandlerTest extends TestCase
{
    /**
     * @see StringTransformer::transform()
     */
    public function testCustomDependencyInjectionHandler(): void
    {
        Util::clearCache();

        ob_start();

        Kernel::init();

        $output = ob_get_clean();

        $this->assertStringContainsString(
            'Generating transformer instance: ' . StringTransformer::class,
            $output,
        );

        $class = new StringClass();

        $this->assertSame(
            'Hello from Code Transformer!',
            $class->test(),
        );
    }
}
