<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Tests\TestApplication\Entity\BlogPost;

class CustomFieldAttributeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title')
            ->setHtmlAttribute('multi-test-one', 'test1')
            ->setHtmlAttribute('multi-test-two', 'test2');

        yield DateTimeField::new('publishedAt')
            ->setHtmlAttributes([
                'data-some-attribute' => 'some-value',
                'x-attribute-two' => 'another value',
                'foo' => 3,
                'bar' => false,
                'foo-bar' => true,
            ]);
    }
}
