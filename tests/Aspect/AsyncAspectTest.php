<?php

namespace Tourze\Symfony\AopAsyncBundle\Aspect;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Tourze\AsyncServiceCallBundle\Message\ServiceCallMessage;
use Tourze\AsyncServiceCallBundle\Service\Serializer;
use Tourze\Symfony\Aop\Model\JoinPoint;
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

class AsyncAspectTest extends TestCase
{
    private AsyncAspect $aspect;
    private MessageBusInterface $messageBus;
    private Serializer $serializer;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->serializer = $this->createMock(Serializer::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->aspect = new AsyncAspect(
            $this->messageBus,
            $this->serializer,
            $this->logger
        );
    }

    public function testHookAsyncWithValidAsync(): void
    {
        $joinPoint = $this->createMock(JoinPoint::class);
        $testService = new TestServiceWithAsync();

        $joinPoint->expects($this->once())
            ->method('getInstance')
            ->willReturn($testService);

        $joinPoint->expects($this->any())
            ->method('getMethod')
            ->willReturn('asyncMethod');

        $joinPoint->expects($this->once())
            ->method('getInternalServiceId')
            ->willReturn('test.service');

        $joinPoint->expects($this->once())
            ->method('getParams')
            ->willReturn(['param1' => 'value1']);

        $this->serializer->expects($this->once())
            ->method('encodeParams')
            ->with(['param1' => 'value1'])
            ->willReturn(['encoded_param1' => 'encoded_value1']);

        $joinPoint->expects($this->once())
            ->method('setReturnEarly')
            ->with(true);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function (ServiceCallMessage $message) {
                    return $message->getServiceId() === 'test.service' &&
                        $message->getMethod() === 'asyncMethod' &&
                        $message->getParams() === ['encoded_param1' => 'encoded_value1'] &&
                        $message->getMaxRetryCount() === 3 &&
                        $message->getRetryCount() === 3;
                }),
                $this->callback(function (array $stamps) {
                    return count($stamps) === 1 &&
                        $stamps[0] instanceof DelayStamp &&
                        $stamps[0]->getDelay() === 1000;
                })
            )
            ->willReturn(new Envelope(new \stdClass()));

        $this->aspect->hookAsync($joinPoint);
    }

    public function testHookAsyncWithoutDelay(): void
    {
        $joinPoint = $this->createMock(JoinPoint::class);
        $testService = new TestServiceWithAsyncNoDelay();

        $joinPoint->expects($this->once())
            ->method('getInstance')
            ->willReturn($testService);

        $joinPoint->expects($this->any())
            ->method('getMethod')
            ->willReturn('asyncMethodNoDelay');

        $joinPoint->expects($this->once())
            ->method('getInternalServiceId')
            ->willReturn('test.service');

        $joinPoint->expects($this->once())
            ->method('getParams')
            ->willReturn([]);

        $this->serializer->expects($this->once())
            ->method('encodeParams')
            ->with([])
            ->willReturn([]);

        $joinPoint->expects($this->once())
            ->method('setReturnEarly')
            ->with(true);

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(ServiceCallMessage::class),
                []
            )
            ->willReturn(new Envelope(new \stdClass()));

        $this->aspect->hookAsync($joinPoint);
    }

    public function testHookAsyncWithException(): void
    {
        $joinPoint = $this->createMock(JoinPoint::class);
        $testService = new TestServiceWithAsync();

        // getInstance 会被 getAttribute 调用一次
        $joinPoint->expects($this->once())
            ->method('getInstance')
            ->willReturn($testService);

        // getMethod 会被多次调用
        $joinPoint->expects($this->any())
            ->method('getMethod')
            ->willReturn('asyncMethod');

        $joinPoint->expects($this->once())
            ->method('getInternalServiceId')
            ->willReturn('test.service');

        $joinPoint->expects($this->once())
            ->method('getParams')
            ->willReturn([]);

        $this->serializer->expects($this->once())
            ->method('encodeParams')
            ->willReturn([]);

        // 在 dispatch 时抛出异常
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->willThrowException(new \RuntimeException('Dispatch failed'));

        // 当抛出异常时，setReturnEarly 不会被调用
        $joinPoint->expects($this->never())
            ->method('setReturnEarly');

        $this->logger->expects($this->once())
            ->method('error')
            ->with(
                '异步执行服务逻辑失败，尝试直接同步执行',
                $this->callback(function ($context) use ($joinPoint) {
                    return isset($context['exception']) &&
                        $context['exception'] instanceof \RuntimeException &&
                        $context['joinPoint'] === $joinPoint;
                })
            );

        $this->aspect->hookAsync($joinPoint);
    }
}

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