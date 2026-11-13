<?php

namespace Okapi\CodeTransformer\Tests\Unit;

use Okapi\CodeTransformer\Core\Cache\CacheState;
use Okapi\CodeTransformer\Core\Cache\CacheState\TransformedCacheState;
use Okapi\CodeTransformer\Core\Cache\CacheStateFactory;
use Okapi\CodeTransformer\Core\DI;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;

class CacheStateTest extends TestCase
{
    public function testTransformedStateRoundTrip(): void
    {
        $data = [
            CacheState::ORIGINAL_FILE_PATH_KEY => __FILE__,
            CacheState::NAMESPACED_CLASS_KEY => self::class,
            CacheState::MODIFICATION_TIME_KEY => time(),
            TransformedCacheState::TRANSFORMED_FILE_PATH_KEY => __FILE__,
            TransformedCacheState::TRANSFORMER_FILE_PATHS_KEY => [__FILE__],
        ];
        $state = new TransformedCacheState($data);
        $serialized = $state->toArray();
        static::assertSame(['type' => 'TransformedCacheState', ...$data], $serialized);

        unset($serialized[CacheState::TYPE]);
        $restored = (new TransformedCacheState())->createIfValid($serialized);
        static::assertInstanceOf(TransformedCacheState::class, $restored);
        static::assertSame($state->toArray(), $restored->toArray());
        static::assertTrue($restored->isFresh());
        static::assertSame(__FILE__, $restored->getFilePath());
    }

    #[RunInSeparateProcess]
    public function testFactorySkipsInvalidEntriesAndRestoresValidState(): void
    {
        DI::getInstance()->register();
        $valid = [
            CacheState::TYPE => 'NoTransformationsCacheState',
            CacheState::ORIGINAL_FILE_PATH_KEY => __FILE__,
            CacheState::NAMESPACED_CLASS_KEY => self::class,
            CacheState::MODIFICATION_TIME_KEY => time(),
        ];
        $states = (new CacheStateFactory())->createCacheStates([
            null,
            'invalid',
            [],
            [CacheState::TYPE => []],
            [CacheState::TYPE => 'UnknownCacheState'],
            [CacheState::TYPE => 'TransformedCacheState'],
            $valid,
        ]);

        static::assertSame([__FILE__], array_keys($states));
        static::assertSame($valid, $states[__FILE__]->toArray());
        static::assertNull($states[__FILE__]->getFilePath());
    }
}
