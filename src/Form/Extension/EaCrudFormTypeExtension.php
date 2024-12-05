<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Extension;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FormVarsDto;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Extension that injects EasyAdmin related information in the view used to
 * render the form.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class EaCrudFormTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('ea_crud_form');
        $resolver->define('ea_vars')->allowedTypes(FormVarsDto::class);

        $resolver->setDeprecated('ea_crud_form', 'easycorp/easyadmin-bundle', 'The "%name%" form option is deprecated since EasyAdmin 4.8.0 and will be removed in 5.0.0. Use the "ea_vars" option instead (e.g. instead of "ea_crud_form.ea_field", use "ea_vars.field").');
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        if (null === $this->adminContextProvider->getContext()) {
            return;
        }

        $view->vars['ea_vars'] = new FormVarsDto(
            fieldDto: $form->getConfig()->getAttribute('ea_field'),
            entityDto: $form->getConfig()->getAttribute('ea_entity')
        );

        // TODO: remove this entire variable in EasyAdmin 5.0.0
        $view->vars['ea_crud_form'] = [
            'form_panel' => $form->getConfig()->getAttribute('ea_form_panel'),
            'form_fieldset' => $form->getConfig()->getAttribute('ea_form_fieldset'),
            'form_tab' => $form->getConfig()->getAttribute('ea_form_tab'),
            'ea_field' => $form->getConfig()->getAttribute('ea_field'),
            'ea_entity' => $form->getConfig()->getAttribute('ea_entity'),
        ];
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
