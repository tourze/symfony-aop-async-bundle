<?php

namespace Tourze\Symfony\AopAsyncBundle\Tests\Aspect;

use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

/**
 * @internal
 */
class TestServiceWithAsyncNoDelay
{
    #[Async]
    public function asyncMethodNoDelay(): void
    {
    }
}
