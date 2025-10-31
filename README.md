# youzan-api-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/youzan-api-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/youzan-api-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/youzan-api-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/youzan-api-bundle)
[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/youzan-api-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/youzan-api-bundle)
[![License](https://img.shields.io/packagist/l/tourze/youzan-api-bundle.svg?style=flat-square)]
(https://packagist.org/packages/tourze/youzan-api-bundle)

A Symfony bundle for integrating with Youzan API, providing account and shop management capabilities.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Entities](#entities)
- [Admin Interface](#admin-interface)
- [Advanced Usage](#advanced-usage)
- [Security](#security)
- [Performance Tips](#performance-tips)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [License](#license)

## Features

- Account management for Youzan API credentials
- Shop management with multi-account support
- Client service for easy API access
- EasyAdmin integration for backend management
- Doctrine ORM integration with Snowflake ID support

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher
- youzanyun/open-sdk 2.0 or higher
- EasyAdmin Bundle 4.0 or higher

## Installation

```bash
composer require tourze/youzan-api-bundle
```

## Quick Start

1. Add the bundle to your `bundles.php`:

```php
<?php

return [
    // ...
    YouzanApiBundle\YouzanApiBundle::class => ['all' => true],
];
```

2. Create and manage Youzan accounts:

```php
<?php

use YouzanApiBundle\Service\YouzanClientService;

// Inject the service
public function __construct(
    private readonly YouzanClientService $youzanClientService
) {
}

// Create a new account
$account = $this->youzanClientService->createAccount(
    'Account Name',
    'your_client_id',
    'your_client_secret'
);

// Get API client
$client = $this->youzanClientService->getClient($account);

// Or get client by client ID
$client = $this->youzanClientService->getClientByClientId('your_client_id');
```

3. Use the client to call Youzan API:

```php
<?php

// Use the client to make API calls
$response = $client->get('/youzan.shop.get', ['version' => '3.0.0']);
```

## Configuration

The bundle registers services automatically. For production use, configure your environment variables:

```env
# .env
YOUZAN_CLIENT_ID=your_client_id
YOUZAN_CLIENT_SECRET=your_client_secret
```

## Entities

### Account
- Manages Youzan API credentials (Client ID, Client Secret)
- Supports multiple shops per account

### Shop
- Represents Youzan shops with KDT ID
- Can be associated with multiple accounts

## Admin Interface

The bundle provides EasyAdmin controllers for managing accounts and shops:

- Account management: Create, edit, and delete API accounts
- Shop management: Manage shop information and associations

## Advanced Usage

For more advanced scenarios, you can extend the entities or create custom services:

```php
<?php

use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;

// Custom account with additional properties
class ExtendedAccount extends Account
{
    private string $description;
    
    // Additional methods...
}

// Custom shop repository
class CustomShopRepository extends ShopRepository
{
    public function findActiveShops(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.active = :active')
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();
    }
}
```

## Security

### Reporting Security Issues

If you discover a security vulnerability, please send an email to security@tourze.com.
All security vulnerabilities will be promptly addressed.

### Security Best Practices

- Store API credentials securely using environment variables
- Use HTTPS for all API communications
- Regularly rotate API credentials
- Implement proper access controls for admin interfaces

## Performance Tips

- Cache API responses when possible to reduce API calls
- Use batch operations for multiple API requests
- Implement connection pooling for high-volume scenarios
- Monitor API rate limits and implement backoff strategies

## Changelog

### Version 1.0.0
- Initial release with basic account and shop management
- EasyAdmin integration for backend management
- Youzan API client service with automatic token handling

## Contributing

We welcome contributions! Here's how you can help:

1. **Report Issues**: Use GitHub Issues to report bugs or request features
2. **Submit Pull Requests**: Fork the repository and submit PRs for improvements
3. **Code Style**: Follow PSR-12 coding standards
4. **Testing**: Ensure all tests pass before submitting PRs

### Development Setup

```bash
# Clone the repository
git clone https://github.com/yourorg/php-monorepo.git

# Install dependencies
composer install

# Run tests
./vendor/bin/phpunit packages/youzan-api-bundle/tests

# Run code analysis
./vendor/bin/phpstan analyse packages/youzan-api-bundle
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.