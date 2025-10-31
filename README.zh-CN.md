# symfony-aop-async-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![License](https://img.shields.io/packagist/l/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

一个基于 AOP（面向切面编程）和 Symfony Messenger 的异步方法执行 Symfony Bundle。

## 特性

- **简单的异步执行**：使用 `#[Async]` 属性标记任何公共方法即可异步执行
- **基于 AOP 的实现**：使用面向切面编程实现清洁透明的异步执行
- **Symfony Messenger 集成**：利用 Symfony Messenger 进行可靠的消息处理
- **可配置重试和延迟**：支持重试次数和执行延迟
- **优雅降级**：如果异步分发失败，会回退到同步执行

## 安装

```bash
composer require tourze/symfony-aop-async-bundle
```

## 快速开始

1. **启用 Bundle** 在你的 `config/bundles.php` 中：

```php
return [
    // ... 其他 bundles
    Tourze\Symfony\AopAsyncBundle\AopAsyncBundle::class => ['all' => true],
];
```

2. **标记方法为异步执行**：

```php
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

class EmailService
{
    #[Async]
    public function sendWelcomeEmail(string $userEmail): void
    {
        // 这个方法将异步执行
        // 发送邮件逻辑...
    }
    
    #[Async(retryCount: 3, delayMs: 5000)]
    public function processLargeDataset(array $data): void
    {
        // 这个方法最多重试 3 次
        // 并延迟 5 秒后执行
    }
}
```

3. **正常使用服务**：

```php
class UserController extends AbstractController
{
    public function register(EmailService $emailService): Response
    {
        // 这个调用立即返回，邮件异步发送
        $emailService->sendWelcomeEmail('user@example.com');
        
        return new Response('用户注册成功');
    }
}
```

## 配置

### Async 属性选项

`#[Async]` 属性支持以下选项：

- `retryCount` (int, 默认: 0)：方法失败时的重试次数
- `delayMs` (int, 默认: 0)：执行方法前的延迟毫秒数

```php
#[Async(retryCount: 5, delayMs: 10000)]
public function criticalTask(): void
{
    // 这个任务最多重试 5 次
    // 并延迟 10 秒后执行
}
```

### Messenger 配置

在你的 `config/packages/messenger.yaml` 中配置 Symfony Messenger：

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'Tourze\AsyncServiceCallBundle\Message\ServiceCallMessage': async
```

## 限制

为了保持实现简单，目前有以下限制：

1. **仅限公共方法**：由于 AOP 限制，`#[Async]` 属性只能用在公共方法上
2. **对象序列化**：复杂的嵌套对象和对象数组可能无法正确序列化
3. **不继承事务**：异步方法不会继承数据库事务
4. **异常处理**：异步方法中的异常会被记录但不会传播给调用者

## 工作原理

1. 当调用标记了 `#[Async]` 的方法时，AsyncAspect 拦截调用
2. 方法调用被序列化为 ServiceCallMessage
3. 消息通过 Symfony Messenger 分发，带有可选的延迟/重试标记
4. 原始方法调用立即返回，不执行方法体
5. 消息由 Messenger worker 异步处理
6. 如果异步分发失败，方法回退到同步执行

## 测试

运行测试套件：

```bash
./vendor/bin/phpunit packages/symfony-aop-async-bundle/tests
```

## 依赖

此 Bundle 需要：

- `tourze/symfony-aop-bundle`：提供 AOP 功能
- `tourze/async-service-call-bundle`：处理异步服务调用
- `symfony/messenger`：消息队列系统

## 贡献

请参阅 [CONTRIBUTING.md](../../CONTRIBUTING.md) 了解详情。

## 安全

如果您发现任何安全相关问题，请在我们的 [GitHub 仓库](https://github.com/tourze/php-monorepo/issues) 中创建 issue。

## 许可证

MIT 许可证。详见 [LICENSE](LICENSE) 文件。
