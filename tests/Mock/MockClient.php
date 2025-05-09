<?php

namespace YouzanApiBundle\Tests\Mock;

/**
 * 模拟Youzan\Open\Client类，用于测试
 */
class MockClient
{
    private string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * 获取访问令牌
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * 模拟API请求
     */
    public function post(string $apiName, array $params = []): array
    {
        // 返回模拟的API响应
        return [
            'success' => true,
            'data' => [
                'api' => $apiName,
                'params' => $params,
                'access_token' => $this->accessToken,
            ],
        ];
    }

    /**
     * 模拟API请求
     */
    public function get(string $apiName, array $params = []): array
    {
        // 返回模拟的API响应
        return [
            'success' => true,
            'data' => [
                'api' => $apiName,
                'params' => $params,
                'access_token' => $this->accessToken,
            ],
        ];
    }
} 