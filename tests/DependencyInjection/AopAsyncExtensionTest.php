<?php

namespace Tourze\Symfony\AopAsyncBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use Tourze\Symfony\AopAsyncBundle\DependencyInjection\AopAsyncExtension;

/**
 * @internal
 */
#[CoversClass(AopAsyncExtension::class)]
final class AopAsyncExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    public function testExtensionHasCorrectAlias(): void
    {
        $extension = new AopAsyncExtension();
        $this->assertSame('aop_async', $extension->getAlias());
    }
}
