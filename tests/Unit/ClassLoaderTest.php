<?php

namespace Okapi\CodeTransformer\Tests\Unit;

use Composer\Autoload\ClassLoader as ComposerClassLoader;
use Okapi\CodeTransformer\Core\AutoloadInterceptor\ClassLoader;
use PHPUnit\Framework\TestCase;

class ClassLoaderTest extends TestCase
{
    public function testMissingClassFollowsComposerAutoloadContract(): void
    {
        $loader = new ClassLoader(new ComposerClassLoader());

        static::assertFalse($loader->findFile('Nonexistent\\MissingClass'));
        static::assertNull($loader->loadClass('Nonexistent\\MissingClass'));
    }
}
