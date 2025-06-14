<?php

namespace Tourze\Symfony\AopAsyncBundle\Attribute;

/**
 * 标记方法为异步执行，必须用在public方法上，这是AOP设计的限制
 *
 * 为了简化实现，目前做出下面的一些限制：
 *
 * 1. 对对象做了部分处理，但是对于复杂的对象数组没办法处理
 * 2. 因为异步执行是丢到队列跑的，所以这里不会继承事务
 * 3. 异常也无法处理
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Async
{
    public function __construct(
        public int $retryCount = 0,
        public int $delayMs = 0,
    )
    {
    }
}
