<?php

namespace YouzanApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use YouzanApiBundle\Entity\Account;

/**
 * 有赞账号测试数据夹具
 *
 * 创建不同类型的有赞账号，用于演示和测试有赞API功能
 */
#[When(env: 'test')]
class AccountFixtures extends Fixture
{
    public const ACCOUNT_MAIN_REFERENCE = 'account-main';
    public const ACCOUNT_TEST_REFERENCE = 'account-test';
    public const ACCOUNT_DEMO_REFERENCE = 'account-demo';

    public function load(ObjectManager $manager): void
    {
        $account1 = new Account();
        $account1->setName('主账号');
        $account1->setClientId('main_client_id_123456');
        $account1->setClientSecret('main_client_secret_abcdef');

        $manager->persist($account1);

        $account2 = new Account();
        $account2->setName('测试账号');
        $account2->setClientId('test_client_id_789012');
        $account2->setClientSecret('test_client_secret_ghijkl');

        $manager->persist($account2);

        $account3 = new Account();
        $account3->setName('演示账号');
        $account3->setClientId('demo_client_id_345678');
        $account3->setClientSecret('demo_client_secret_mnopqr');

        $manager->persist($account3);

        $manager->flush();

        $this->addReference(self::ACCOUNT_MAIN_REFERENCE, $account1);
        $this->addReference(self::ACCOUNT_TEST_REFERENCE, $account2);
        $this->addReference(self::ACCOUNT_DEMO_REFERENCE, $account3);
    }
}
