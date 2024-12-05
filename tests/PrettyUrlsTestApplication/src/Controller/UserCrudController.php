<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Tests\PrettyUrlsTestApplication\Entity\User;
use Symfony\Component\HttpFoundation\Response;

#[AdminCrud('/user-editor', 'external_user_editor')]
class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('email');
    }

    #[AdminAction(routePath: '/custom/path-for-index', routeName: 'custom_route_for_index')]
    public function index(AdminContext $context)
    {
        return parent::index($context);
    }

    #[AdminAction(routePath: '/custom/path-for-detail/{entityId}')]
    public function detail(AdminContext $context)
    {
        return parent::detail($context);
    }

    #[AdminAction(routeName: 'custom_route_for_new')]
    public function new(AdminContext $context)
    {
        return parent::new($context);
    }

    // this action doesn't use the #[AdminAction] attribute on purpose to test default behavior
    public function edit(AdminContext $context)
    {
        return parent::edit($context);
    }

    #[AdminAction(routeName: 'foobar', routePath: '/bar/foo')]
    public function someCustomAction(): Response
    {
        return new Response('This is a custom action');
    }

    #[AdminAction('/bar/bar', 'foofoo')]
    public function anotherCustomActionWithoutPropertyNames(): Response
    {
        return new Response('This is custom action with short attribute syntax');
    }

    // this custom action doesn't use the #[AdminAction] attribute on purpose to test default behavior
    public function anotherCustomAction(): Response
    {
        return new Response('This is another custom action');
    }
}
