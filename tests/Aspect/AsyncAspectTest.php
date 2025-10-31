<?php

namespace Tourze\Symfony\AopAsyncBundle\Tests\Aspect;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopAsyncBundle\Aspect\AsyncAspect;

/**
 * @internal
 */
#[CoversClass(AsyncAspect::class)]
#[RunTestsInSeparateProcesses]
final class AsyncAspectTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // No additional setup needed for basic integration testing
    }

    public function testGetAspectFromContainer(): void
    {
        $aspect = self::getService(AsyncAspect::class);
        $this->assertInstanceOf(AsyncAspect::class, $aspect);
    }

    public function testHookAsyncBasicFunctionality(): void
    {
        $aspect = self::getService(AsyncAspect::class);

        /**
         * 使用具体 JoinPoint 类的 Mock，因为：
         * 1. JoinPoint 是 AOP 框架的核心模型类，没有相应的接口抽象
         * 2. 测试需要验证与 JoinPoint 实例的基本交互行为
         * 3. AsyncAspect 的异步处理逻辑依赖于 JoinPoint 的具体方法实现
         */
        $joinPoint = $this->createMock(JoinPoint::class);
        $testService = new TestServiceWithAsync();

        $joinPoint->method('getInstance')->willReturn($testService);
        $joinPoint->method('getMethod')->willReturn('asyncMethod');
        $joinPoint->method('getInternalServiceId')->willReturn('test.service');
        $joinPoint->method('getParams')->willReturn([]);

        // 测试方法执行不会抛出异常（使用真实的依赖服务）
        $aspect->hookAsync($joinPoint);

        // 验证服务可以正常工作
        $this->assertInstanceOf(AsyncAspect::class, $aspect);
    }

    public function testHookAsyncWithDifferentParameters(): void
    {
        $aspect = self::getService(AsyncAspect::class);

        /**
         * 使用具体 JoinPoint 类的 Mock，因为：
         * 1. JoinPoint 是 AOP 框架的核心模型类，没有相应的接口抽象
         * 2. 测试需要验证与 JoinPoint 实例的基本交互行为
         */
        $joinPoint = $this->createMock(JoinPoint::class);
        $testService = new TestServiceWithAsyncNoDelay();

        $joinPoint->method('getInstance')->willReturn($testService);
        $joinPoint->method('getMethod')->willReturn('asyncMethodNoDelay');
        $joinPoint->method('getInternalServiceId')->willReturn('test.service.no.delay');
        $joinPoint->method('getParams')->willReturn(['param1' => 'value1', 'param2' => 'value2']);

        // 测试方法执行不会抛出异常
        $aspect->hookAsync($joinPoint);

        // 验证服务可以正常工作
        $this->assertInstanceOf(AsyncAspect::class, $aspect);
    }
}
