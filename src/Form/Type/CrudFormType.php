<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Type;

use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Form\EventListener\FormLayoutSubscriber;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormColumnOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormFieldsetCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormFieldsetOpenType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormRowType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneCloseType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout\EaFormTabPaneOpenType;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form type that deals with some of the logic used to render the
 * forms used to create and edit EasyAdmin entities.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class CrudFormType extends AbstractType
{
    private DoctrineOrmTypeGuesser $doctrineOrmTypeGuesser;

    public function __construct(DoctrineOrmTypeGuesser $doctrineOrmTypeGuesser)
    {
        $this->doctrineOrmTypeGuesser = $doctrineOrmTypeGuesser;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var EntityDto $entityDto */
        $entityDto = $options['entityDto'];

        /** @var FieldDto $fieldDto */
        foreach ($entityDto->getFields() as $fieldDto) {
            $formFieldOptions = $fieldDto->getFormTypeOptions();

            // the names of embedded Doctrine entities contain dots, which are not allowed
            // in HTML element names. In those cases, fix the name but also update the
            // 'property_path' option to keep the original field name
            if (str_contains($fieldDto->getProperty(), '.')) {
                $formFieldOptions['property_path'] = $fieldDto->getProperty();
                $name = str_replace(['.', '[', ']', '?'], '_', $fieldDto->getProperty());
            } else {
                $name = $fieldDto->getProperty();
            }

            if (null === $formFieldType = $fieldDto->getFormType()) {
                $guessType = $this->doctrineOrmTypeGuesser->guessType($entityDto->getFqcn(), $fieldDto->getProperty());
                $formFieldType = $guessType->getType();
                $formFieldOptions = array_merge($guessType->getOptions(), $formFieldOptions);
            }

            $name = $this->isTypeFormField($formFieldType) ? $fieldDto->getPropertyNameWithSuffix() : $name;

            $formField = $builder->getFormFactory()->createNamedBuilder($name, $formFieldType, null, $formFieldOptions);
            $formField->setAttribute('ea_entity', $entityDto);
            $formField->setAttribute('ea_field', $fieldDto);

            $builder->add($formField);
        }

        $builder->addEventSubscriber(new FormLayoutSubscriber());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'allow_extra_fields' => true,
                'data_class' => static fn (Options $options, $dataClass) => $dataClass ?? $options['entityDto']->getFqcn(),
            ])
            ->setDefined(['entityDto'])
            ->setRequired(['entityDto']);
    }

    public function getBlockPrefix(): string
    {
        return 'ea_crud';
    }

    private function isTypeFormField(?string $type): bool
    {
        if (null === $type) {
            return false;
        }

        return \in_array($type, [
            EaFormFieldsetOpenType::class,
            EaFormFieldsetCloseType::class,

            EaFormRowType::class,

            EaFormTabPaneOpenType::class,
            EaFormTabPaneCloseType::class,

            EaFormColumnOpenType::class,
            EaFormColumnCloseType::class,
        ], true);
    }
}
