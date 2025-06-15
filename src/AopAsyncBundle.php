<?php

namespace Tourze\Symfony\AopAsyncBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\AsyncServiceCallBundle\AsyncServiceCallBundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\Symfony\Aop\AopBundle;

class AopAsyncBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            AsyncServiceCallBundle::class => ['all' => true],
            AopBundle::class => ['all' => true],
        ];
    }
}
