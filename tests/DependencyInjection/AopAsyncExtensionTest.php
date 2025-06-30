<?php

namespace Tourze\Symfony\AopAsyncBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\Symfony\AopAsyncBundle\Aspect\AsyncAspect;

class AopAsyncExtensionTest extends TestCase
{
    private AopAsyncExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new AopAsyncExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad(): void
    {
        $this->extension->load([], $this->container);

        // 验证资源已被加载（由于使用 resource 加载，我们检查参数或其他设置）
        $this->assertNotEmpty($this->container->getDefinitions());
        
        // 验证至少有一些定义被加载
        $definitions = $this->container->getDefinitions();
        $hasAspectDefinition = false;
        
        foreach ($definitions as $id => $definition) {
            if (str_contains($id, 'Aspect')) {
                $hasAspectDefinition = true;
                break;
            }
        }
        
        $this->assertTrue($hasAspectDefinition, 'Should have loaded aspect definitions');
    }

    public function testLoadWithEmptyConfig(): void
    {
        $this->extension->load([], $this->container);

        // 即使配置为空，服务也应该被加载
        $this->assertNotEmpty($this->container->getDefinitions());
    }

    public function testLoadMultipleTimes(): void
    {
        // 测试多次加载不会出错
        $this->extension->load([], $this->container);
        $this->extension->load([], $this->container);

        $this->assertNotEmpty($this->container->getDefinitions());
    }
}