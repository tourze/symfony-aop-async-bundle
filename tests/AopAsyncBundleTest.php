<?php

declare(strict_types=1);

namespace Tourze\Symfony\AopAsyncBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use Tourze\Symfony\AopAsyncBundle\AopAsyncBundle;

/**
 * @internal
 */
#[CoversClass(AopAsyncBundle::class)]
#[RunTestsInSeparateProcesses]
final class AopAsyncBundleTest extends AbstractBundleTestCase
{
}
