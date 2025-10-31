<?php

namespace YouzanApiBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;

/**
 * 有赞店铺测试数据夹具
 *
 * 创建不同类型的有赞店铺，用于演示和测试店铺管理功能
 */
#[When(env: 'test')]
class ShopFixtures extends Fixture implements DependentFixtureInterface
{
    public const SHOP_FLAGSHIP_REFERENCE = 'shop-flagship';
    public const SHOP_BRANCH_REFERENCE = 'shop-branch';
    public const SHOP_DEMO_REFERENCE = 'shop-demo';

    public function load(ObjectManager $manager): void
    {
        $shop1 = new Shop();
        $shop1->setKdtId(12345678);
        $shop1->setName('旗舰店');
        $shop1->setLogo('/images/shop/flagship-logo.png');

        $account1 = $this->getReference(AccountFixtures::ACCOUNT_MAIN_REFERENCE, Account::class);
        $shop1->addAccount($account1);

        $manager->persist($shop1);

        $shop2 = new Shop();
        $shop2->setKdtId(87654321);
        $shop2->setName('分店');
        $shop2->setLogo('/images/shop/branch-logo.png');

        $account2 = $this->getReference(AccountFixtures::ACCOUNT_TEST_REFERENCE, Account::class);
        $shop2->addAccount($account2);

        $manager->persist($shop2);

        $shop3 = new Shop();
        $shop3->setKdtId(11111111);
        $shop3->setName('演示店铺');

        $account3 = $this->getReference(AccountFixtures::ACCOUNT_DEMO_REFERENCE, Account::class);
        $shop3->addAccount($account3);

        $manager->persist($shop3);

        $manager->flush();

        $this->addReference(self::SHOP_FLAGSHIP_REFERENCE, $shop1);
        $this->addReference(self::SHOP_BRANCH_REFERENCE, $shop2);
        $this->addReference(self::SHOP_DEMO_REFERENCE, $shop3);
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
