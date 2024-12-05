<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Config\Action as AppAction;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\Category;
use Symfony\Component\HttpFoundation\Response;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return parent::configureAssets($assets)
            ->addHtmlContentToHead('<link data-added-from-controller rel="me" href="https://example.com">')
            ->addHtmlContentToBody('<span data-added-from-controller><!-- foo --></span>')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('slug'),
            BooleanField::new('active'),
            BooleanField::new('activeWithNoPermission')->setPermission('ROLE_FOO'),
            BooleanField::new('activeDisabled')->setDisabled(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $customPageAction = Action::new(AppAction::CUSTOM_ACTION)
            ->createAsGlobalAction()
            ->linkToCrudAction('customPage');

        return $actions->add(Crud::PAGE_INDEX, $customPageAction)
            ->setPermission(AppAction::CUSTOM_ACTION, 'ROLE_ADMIN');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('active');
    }

    public function customAction(AdminContext $context): Response
    {
        if (!$this->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => AppAction::CUSTOM_ACTION, 'entity' => $context->getEntity()])) {
            throw new ForbiddenActionException($context);
        }

        return new Response();
    }
}
