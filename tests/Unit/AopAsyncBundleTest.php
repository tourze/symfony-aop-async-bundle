<?php

namespace Tourze\Symfony\AopAsyncBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tourze\AsyncServiceCallBundle\AsyncServiceCallBundle;
use Tourze\Symfony\Aop\AopBundle;
use Tourze\Symfony\AopAsyncBundle\AopAsyncBundle;

class AopAsyncBundleTest extends TestCase
{
    public function testBundleDependencies(): void
    {
        $dependencies = AopAsyncBundle::getBundleDependencies();
        
        $this->assertArrayHasKey(AsyncServiceCallBundle::class, $dependencies);
        $this->assertArrayHasKey(AopBundle::class, $dependencies);
        
        $this->assertEquals(['all' => true], $dependencies[AsyncServiceCallBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[AopBundle::class]);
    }
    
    public function testInstanceOfBundle(): void
    {
        $bundle = new AopAsyncBundle();
        $this->assertInstanceOf(\Symfony\Component\HttpKernel\Bundle\Bundle::class, $bundle);
    }
    
    public function testImplementsBundleDependencyInterface(): void
    {
        $bundle = new AopAsyncBundle();
        $this->assertInstanceOf(\Tourze\BundleDependency\BundleDependencyInterface::class, $bundle);
    }
}