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
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use YouzanApiBundle\Entity\Shop;

/**
 * 有赞店铺管理控制器
 *
 * @extends AbstractCrudController<Shop>
 */
#[AdminCrud(routePath: '/youzan/shop', routeName: 'youzan_shop')]
final class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('有赞店铺')
            ->setEntityLabelInPlural('有赞店铺管理')
            ->setPageTitle('index', '有赞店铺列表')
            ->setPageTitle('detail', '有赞店铺详情')
            ->setPageTitle('new', '新增有赞店铺')
            ->setPageTitle('edit', '编辑有赞店铺')
            ->setHelp('index', '管理有赞平台的店铺信息，包括店铺ID、名称和Logo等')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'kdtId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield IntegerField::new('kdtId', '有赞店铺ID')
            ->setHelp('有赞平台的店铺唯一标识符')
            ->setFormTypeOptions([
                'attr' => [
                    'min' => 1,
                    'placeholder' => '请输入有赞店铺ID',
                ],
            ])
        ;

        yield TextField::new('name', '店铺名称')
            ->setHelp('店铺的显示名称')
        ;

        yield TextField::new('logo', '店铺Logo')
            ->setHelp('店铺的Logo图片URL')
            ->hideOnIndex()
            ->setFormTypeOptions([
                'attr' => [
                    'placeholder' => '请输入Logo图片URL',
                ],
            ])
        ;

        yield AssociationField::new('accounts', '关联账号')
            ->setHelp('可以访问该店铺的有赞账号')
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
            ->add(NumericFilter::new('kdtId', '有赞店铺ID'))
            ->add(TextFilter::new('name', '店铺名称'))
            ->add(EntityFilter::new('accounts', '关联账号'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
