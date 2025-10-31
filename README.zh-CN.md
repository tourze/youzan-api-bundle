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

用于集成有赞API的Symfony Bundle，提供账号和店铺管理功能。

## 目录

- [功能特性](#功能特性)
- [系统要求](#系统要求)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置](#配置)
- [实体](#实体)
- [管理界面](#管理界面)
- [高级用法](#高级用法)
- [安全](#安全)
- [性能优化建议](#性能优化建议)
- [更新日志](#更新日志)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- 有赞API凭证账号管理
- 多账号支持的店铺管理
- 便捷的API访问客户端服务
- EasyAdmin后台管理集成
- 支持雪花ID的Doctrine ORM集成

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本
- youzanyun/open-sdk 2.0 或更高版本
- EasyAdmin Bundle 4.0 或更高版本

## 安装

```bash
composer require tourze/youzan-api-bundle
```

## 快速开始

1. 将Bundle添加到您的 `bundles.php`：

```php
<?php

return [
    // ...
    YouzanApiBundle\YouzanApiBundle::class => ['all' => true],
];
```

2. 创建和管理有赞账号：

```php
<?php

use YouzanApiBundle\Service\YouzanClientService;

// 注入服务
public function __construct(
    private readonly YouzanClientService $youzanClientService
) {
}

// 创建新账号
$account = $this->youzanClientService->createAccount(
    '账号名称',
    'your_client_id',
    'your_client_secret'
);

// 获取API客户端
$client = $this->youzanClientService->getClient($account);

// 或通过客户端ID获取客户端
$client = $this->youzanClientService->getClientByClientId('your_client_id');
```

3. 使用客户端调用有赞API：

```php
<?php

// 使用客户端进行API调用
$response = $client->get('/youzan.shop.get', ['version' => '3.0.0']);
```

## 配置

Bundle自动注册服务。生产环境请配置环境变量：

```env
# .env
YOUZAN_CLIENT_ID=your_client_id
YOUZAN_CLIENT_SECRET=your_client_secret
```

## 实体

### Account（账号）
- 管理有赞API凭证（Client ID, Client Secret）
- 支持每个账号关联多个店铺

### Shop（店铺）
- 表示有赞店铺，包含KDT ID
- 可以关联多个账号

## 管理界面

Bundle提供EasyAdmin控制器用于管理账号和店铺：

- 账号管理：创建、编辑和删除API账号
- 店铺管理：管理店铺信息和关联关系

## 高级用法

对于更高级的场景，您可以扩展实体或创建自定义服务：

```php
<?php

use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;

// 带有额外属性的自定义账号
class ExtendedAccount extends Account
{
    private string $description;
    
    // 额外方法...
}

// 自定义店铺仓库
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

## 安全

### 报告安全问题

如果您发现了安全漏洞，请发送邮件至 security@tourze.com。
所有安全漏洞将得到及时处理。

### 安全最佳实践

- 使用环境变量安全存储API凭证
- 对所有API通信使用HTTPS
- 定期轮换API凭证
- 为管理界面实施适当的访问控制

## 性能优化建议

- 适当缓存API响应以减少API调用次数
- 对多个API请求使用批量操作
- 高并发场景下实施连接池
- 监控API调用频率限制并实施退避策略

## 更新日志

### 版本 1.0.0
- 首次发布，包含基础账号和店铺管理功能
- EasyAdmin后台管理集成
- 有赞API客户端服务，支持自动令牌处理

## 贡献

欢迎贡献！您可以通过以下方式帮助我们：

1. **报告问题**：使用GitHub Issues报告错误或请求新功能
2. **提交Pull Request**：Fork仓库并提交改进的PR
3. **代码风格**：遵循PSR-12编码标准
4. **测试**：提交PR前确保所有测试通过

### 开发环境设置

```bash
# 克隆仓库
git clone https://github.com/yourorg/php-monorepo.git

# 安装依赖
composer install

# 运行测试
./vendor/bin/phpunit packages/youzan-api-bundle/tests

# 运行代码分析
./vendor/bin/phpstan analyse packages/youzan-api-bundle
```

## 许可证

MIT许可证。请查看 [License File](LICENSE) 获取更多信息。