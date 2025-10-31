# symfony-aop-async-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![License](https://img.shields.io/packagist/l/tourze/symfony-aop-async-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/symfony-aop-async-bundle)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A Symfony bundle that provides asynchronous method execution using AOP (Aspect-Oriented Programming) and Symfony Messenger.

## Features

- **Easy async execution**: Mark any public method with `#[Async]` attribute to execute asynchronously
- **AOP-based implementation**: Uses aspect-oriented programming for clean and transparent async execution
- **Symfony Messenger integration**: Leverages Symfony Messenger for reliable message processing
- **Configurable retry and delay**: Support for retry count and execution delay
- **Graceful fallback**: Falls back to synchronous execution if async dispatch fails

## Installation

```bash
composer require tourze/symfony-aop-async-bundle
```

## Quick Start

1. **Enable the bundle** in your `config/bundles.php`:

```php
return [
    // ... other bundles
    Tourze\Symfony\AopAsyncBundle\AopAsyncBundle::class => ['all' => true],
];
```

2. **Mark methods for async execution**:

```php
use Tourze\Symfony\AopAsyncBundle\Attribute\Async;

class EmailService
{
    #[Async]
    public function sendWelcomeEmail(string $userEmail): void
    {
        // This method will be executed asynchronously
        // Send email logic here...
    }
    
    #[Async(retryCount: 3, delayMs: 5000)]
    public function processLargeDataset(array $data): void
    {
        // This method will be retried up to 3 times
        // and delayed by 5 seconds before execution
    }
}
```

3. **Use the service normally**:

```php
class UserController extends AbstractController
{
    public function register(EmailService $emailService): Response
    {
        // This call returns immediately, email is sent asynchronously
        $emailService->sendWelcomeEmail('user@example.com');
        
        return new Response('User registered successfully');
    }
}
```

## Configuration

### Async Attribute Options

The `#[Async]` attribute supports the following options:

- `retryCount` (int, default: 0): Number of times to retry the method if it fails
- `delayMs` (int, default: 0): Delay in milliseconds before executing the method

```php
#[Async(retryCount: 5, delayMs: 10000)]
public function criticalTask(): void
{
    // This task will be retried up to 5 times
    // and delayed by 10 seconds before execution
}
```

### Messenger Configuration

Configure Symfony Messenger in your `config/packages/messenger.yaml`:

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'Tourze\AsyncServiceCallBundle\Message\ServiceCallMessage': async
```

## Limitations

To keep the implementation simple, the following limitations apply:

1. **Public methods only**: The `#[Async]` attribute only works on public methods due to AOP limitations
2. **Object serialization**: Complex nested objects and object arrays may not serialize properly
3. **No transaction inheritance**: Async methods don't inherit database transactions
4. **Exception handling**: Exceptions in async methods are logged but not propagated to the caller

## How It Works

1. When a method marked with `#[Async]` is called, the AsyncAspect intercepts the call
2. The method call is serialized into a ServiceCallMessage
3. The message is dispatched to Symfony Messenger with optional delay/retry stamps
4. The original method call returns immediately without executing the method body
5. The message is processed asynchronously by a Messenger worker
6. If async dispatch fails, the method falls back to synchronous execution

## Testing

Run the test suite:

```bash
./vendor/bin/phpunit packages/symfony-aop-async-bundle/tests
```

## Dependencies

This bundle requires:

- `tourze/symfony-aop-bundle`: Provides AOP functionality
- `tourze/async-service-call-bundle`: Handles async service calls
- `symfony/messenger`: Message queue system

## Contributing

Please see [CONTRIBUTING.md](../../CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please create an issue in our [GitHub repository](https://github.com/tourze/php-monorepo/issues).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.