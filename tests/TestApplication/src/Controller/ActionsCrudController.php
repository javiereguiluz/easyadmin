<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\Category;
use function Symfony\Component\Translation\t;

/**
 * Tests the configureActions() method and the generated actions.
 */
class ActionsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $action1 = Action::new('action1')->linkToCrudAction('');
        $action2 = Action::new('action2')->linkToCrudAction('')->setCssClass('foo');
        $action3 = Action::new('action3')->linkToCrudAction('')->addCssClass('bar');
        $action4 = Action::new('action4')->linkToCrudAction('')->setCssClass('foo')->addCssClass('bar');
        $action5 = Action::new('action5')->linkToCrudAction('')->setLabel(fn (Category $category) => 'Action 5: '.$category->getName());

        $labelGenerator = new LabelGenerator();
        $action6 = Action::new('action6')->linkToCrudAction('')->setLabel(fn (Category $category) => $labelGenerator->generateLabel($category));

        $action7 = Action::new('action7')->linkToCrudAction('')->setLabel(fn (Category $category) => t('Action %number%: %name%', ['%number%' => 7, '%name%' => $category->getName()]));

        // this tests that the 'Reset' label is interpreted as a string and not as a callable to the PHP reset() function
        $action8 = Action::new('action8')->linkToCrudAction('')->setLabel('Reset');

        return $actions
            ->add(Crud::PAGE_INDEX, $action1)
            ->add(Crud::PAGE_INDEX, $action2)
            ->add(Crud::PAGE_INDEX, $action3)
            ->add(Crud::PAGE_INDEX, $action4)
            ->add(Crud::PAGE_INDEX, $action5)
            ->add(Crud::PAGE_INDEX, $action6)
            ->add(Crud::PAGE_INDEX, $action7)
            ->add(Crud::PAGE_INDEX, $action8)
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-fw fa-plus')->setLabel(false);
            })
        ;
    }
}

final class LabelGenerator
{
    public function generateLabel(Category $category): string
    {
        return 'Action 6: '.$category->getName();
    }
}
