<?php

namespace YouzanApiBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use YouzanApiBundle\Entity\Account;

/**
 * 有赞账号管理控制器
 *
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(routePath: '/youzan/account', routeName: 'youzan_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('有赞账号')
            ->setEntityLabelInPlural('有赞账号管理')
            ->setPageTitle('index', '有赞账号列表')
            ->setPageTitle('detail', '有赞账号详情')
            ->setPageTitle('new', '新增有赞账号')
            ->setPageTitle('edit', '编辑有赞账号')
            ->setHelp('index', '管理有赞开放平台的API账号，包括应用ID和密钥配置')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'clientId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('name', '账号名称')
            ->setHelp('便于识别的账号名称，如"生产环境账号"')
        ;

        yield TextField::new('clientId', '客户端ID')
            ->setHelp('有赞开放平台提供的应用ID')
        ;

        yield TextareaField::new('clientSecret', '客户端密钥')
            ->setHelp('有赞开放平台提供的应用密钥')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => [
                    'rows' => 3,
                    'placeholder' => '请输入客户端密钥',
                ],
            ])
        ;

        yield AssociationField::new('shops', '关联店铺')
            ->setHelp('该账号可以访问的有赞店铺')
            ->hideOnForm()
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->setFormat('yyyy-MM-dd HH:mm:ss')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '账号名称'))
            ->add(TextFilter::new('clientId', '客户端ID'))
            ->add(EntityFilter::new('shops', '关联店铺'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
