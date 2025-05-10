<?php

// 注册自动加载
require_once __DIR__ . '/../../../vendor/autoload.php';

// 先加载模拟类
require_once __DIR__ . '/Mock/MockToken.php';
require_once __DIR__ . '/Mock/MockClient.php';

// 映射Youzan SDK类到我们的模拟类
// 注意：这是在测试环境中模拟外部依赖的方法，不适用于生产环境
if (!class_exists('Youzan\Open\Token', false)) {
    class_alias(\YouzanApiBundle\Tests\Mock\MockToken::class, 'Youzan\Open\Token');
}

if (!class_exists('Youzan\Open\Client', false)) {
    class_alias(\YouzanApiBundle\Tests\Mock\MockClient::class, 'Youzan\Open\Client');
} 