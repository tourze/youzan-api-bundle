<?php

namespace YouzanApiBundle\Tests\Helper;

use Doctrine\ORM\EntityManagerInterface;

/**
 * 测试性能优化辅助类
 */
final class TestPerformanceHelper
{
    public static function withTransaction(EntityManagerInterface $em, callable $testLogic): void
    {
        $em->beginTransaction();
        try {
            $testLogic();
        } finally {
            $em->rollback();
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function generateUniqueTestData(string $prefix = 'test'): array
    {
        $uniqueId = uniqid();

        return [
            'id' => $uniqueId,
            'timestamp' => time(),
            'name' => $prefix . '_' . $uniqueId,
            'clientId' => 'client_' . $uniqueId,
            'secret' => 'secret_' . $uniqueId,
            'kdtId' => random_int(100000, 999999) + hexdec(substr($uniqueId, -6)),
        ];
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function createMinimalTestData(array $overrides = []): array
    {
        $defaults = self::generateUniqueTestData();

        return array_merge($defaults, $overrides);
    }
}
