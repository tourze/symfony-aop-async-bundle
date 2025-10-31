<?php

namespace Tourze\Symfony\AopAsyncBundle\Tests\Aspect;

use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

/**
 * @internal
 */
class TestServiceWithAsync
{
    #[Async(retryCount: 3, delayMs: 1000)]
    public function asyncMethod(string $param1): void
    {
    }
}
