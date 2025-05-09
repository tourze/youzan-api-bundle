<?php

namespace YouzanApiBundle\Tests\Mock;

/**
 * 模拟Youzan\Open\Token类，用于测试
 */
class MockToken
{
    private string $clientId;
    private string $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * 模拟获取Token的行为
     */
    public function getSelfAppToken(string $kdtId): array
    {
        // 返回模拟的Token数据
        return [
            'access_token' => 'mock_token_' . $this->clientId,
            'expires_in' => 7200,
            'scope' => 'test_scope',
            'authority_id' => 'test_authority',
        ];
    }
} 