<?php

// 注册自动加载
require_once __DIR__ . '/../../../vendor/autoload.php';

// 映射Youzan SDK类到我们的模拟类
// 注意：这是在测试环境中模拟外部依赖的方法，不适用于生产环境
class_alias(\YouzanApiBundle\Tests\Mock\MockToken::class, 'Youzan\Open\Token');
class_alias(\YouzanApiBundle\Tests\Mock\MockClient::class, 'Youzan\Open\Client'); 