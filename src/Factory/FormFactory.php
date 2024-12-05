<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FiltersFormType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FormFactory
{
    private FormFactoryInterface $symfonyFormFactory;
    private AdminUrlGeneratorInterface $adminUrlGenerator;

    public function __construct(FormFactoryInterface $symfonyFormFactory, AdminUrlGeneratorInterface $adminUrlGenerator)
    {
        $this->symfonyFormFactory = $symfonyFormFactory;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $cssClass = sprintf('ea-%s-form', $context->getCrud()->getCurrentAction());
        $formOptions->set('attr.class', trim(($formOptions->get('attr.class') ?? '').' '.$cssClass));
        $formOptions->set('attr.id', sprintf('edit-%s-form', $entityDto->getName()));
        $formOptions->set('entityDto', $entityDto);
        $formOptions->setIfNotSet('translation_domain', $context->getI18n()->getTranslationDomain());

        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions->all());
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        return $this->createEditFormBuilder($entityDto, $formOptions, $context)->getForm();
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $cssClass = sprintf('ea-%s-form', $context->getCrud()->getCurrentAction());
        $formOptions->set('attr.class', trim(($formOptions->get('attr.class') ?? '').' '.$cssClass));
        $formOptions->set('attr.id', sprintf('new-%s-form', $entityDto->getName()));
        $formOptions->set('entityDto', $entityDto);
        $formOptions->setIfNotSet('translation_domain', $context->getI18n()->getTranslationDomain());

        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions->all());
    }

    public function createNewForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        return $this->createNewFormBuilder($entityDto, $formOptions, $context)->getForm();
    }

    public function createFiltersForm(FilterCollection $filters, Request $request): FormInterface
    {
        // filtering always returns to the index page of the same CRUD entity and with the same query parameters
        $urlQueryParameters = $request->query->all();
        $actionUrl = $this->adminUrlGenerator->setAll($urlQueryParameters)->setAction(Action::INDEX)->generateUrl();

        $filtersForm = $this->symfonyFormFactory->createNamed('filters', FiltersFormType::class, null, [
            'method' => 'GET',
            'action' => $actionUrl,
            'ea_filters' => $filters,
        ]);

        return $filtersForm->handleRequest($request);
    }
}
