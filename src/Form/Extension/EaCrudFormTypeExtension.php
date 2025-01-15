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
        $resolver->define('ea_vars')->allowedTypes(FormVarsDto::class);
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
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
