<?php

namespace YouzanApiBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;

/**
 * 有赞API管理菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (!$item->getChild('有赞API管理')) {
            $item->addChild('有赞API管理');
        }

        $youzanMenu = $item->getChild('有赞API管理');
        
        // 账号管理菜单
        $youzanMenu->addChild('账号管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-cog');
        
        // 店铺管理菜单
        $youzanMenu->addChild('店铺管理')
            ->setUri($this->linkGenerator->getCurdListPage(Shop::class))
            ->setAttribute('icon', 'fas fa-store');
    }
} 