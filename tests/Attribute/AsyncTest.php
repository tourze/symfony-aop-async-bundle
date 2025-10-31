<?php

declare(strict_types=1);

namespace Tourze\Symfony\AopAsyncBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

/**
 * @internal
 */
#[CoversClass(Async::class)]
final class AsyncTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $async = new Async();

        $this->assertSame(0, $async->retryCount);
        $this->assertSame(0, $async->delayMs);
    }

    public function testCustomValues(): void
    {
        $async = new Async(retryCount: 5, delayMs: 2000);

        $this->assertSame(5, $async->retryCount);
        $this->assertSame(2000, $async->delayMs);
    }

    public function testAttributeTargets(): void
    {
        $reflection = new \ReflectionClass(Async::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertCount(1, $attributes);

        $attribute = $attributes[0]->newInstance();
        $this->assertSame(\Attribute::TARGET_METHOD, $attribute->flags);
    }

    public function testCanBeAppliedToMethod(): void
    {
        $testClass = new class {
            #[Async(retryCount: 3, delayMs: 1000)]
            public function testMethod(): void
            {
            }
        };

        $reflection = new \ReflectionMethod($testClass, 'testMethod');
        $attributes = $reflection->getAttributes(Async::class);

        $this->assertCount(1, $attributes);

        $async = $attributes[0]->newInstance();
        $this->assertSame(3, $async->retryCount);
        $this->assertSame(1000, $async->delayMs);
    }
}
