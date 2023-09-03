<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDtoInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FiltersFormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class FormFactory implements FormFactoryInterface
{
    private FormFactoryInterface $symfonyFormFactory;

    public function __construct(FormFactoryInterface $symfonyFormFactory)
    {
        $this->symfonyFormFactory = $symfonyFormFactory;
    }

    public function createEditFormBuilder(EntityDtoInterface $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $cssClass = sprintf('ea-%s-form', $context->getCrud()->getCurrentAction());
        $formOptions->set('attr.class', trim(($formOptions->get('attr.class') ?? '').' '.$cssClass));
        $formOptions->set('attr.id', sprintf('edit-%s-form', $entityDto->getName()));
        $formOptions->set('entityDto', $entityDto);
        $formOptions->setIfNotSet('translation_domain', $context->getI18n()->getTranslationDomain());

        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions->all());
    }

    public function createEditForm(EntityDtoInterface $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        return $this->createEditFormBuilder($entityDto, $formOptions, $context)->getForm();
    }

    public function createNewFormBuilder(EntityDtoInterface $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $cssClass = sprintf('ea-%s-form', $context->getCrud()->getCurrentAction());
        $formOptions->set('attr.class', trim(($formOptions->get('attr.class') ?? '').' '.$cssClass));
        $formOptions->set('attr.id', sprintf('new-%s-form', $entityDto->getName()));
        $formOptions->set('entityDto', $entityDto);
        $formOptions->setIfNotSet('translation_domain', $context->getI18n()->getTranslationDomain());

        return $this->symfonyFormFactory->createNamedBuilder($entityDto->getName(), CrudFormType::class, $entityDto->getInstance(), $formOptions->all());
    }

    public function createNewForm(EntityDtoInterface $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        return $this->createNewFormBuilder($entityDto, $formOptions, $context)->getForm();
    }

    public function createFiltersForm(FilterCollection $filters, Request $request): FormInterface
    {
        $filtersForm = $this->symfonyFormFactory->createNamed('filters', FiltersFormType::class, null, [
            'method' => 'GET',
            'action' => $request->query->get(EA::REFERRER, ''),
            'ea_filters' => $filters,
        ]);

        return $filtersForm->handleRequest($request);
    }
}
