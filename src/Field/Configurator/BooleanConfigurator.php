<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class BooleanConfigurator implements FieldConfiguratorInterface
{
    private AdminUrlGeneratorInterface $adminUrlGenerator;
    private ?CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(AdminUrlGeneratorInterface $adminUrlGenerator, ?CsrfTokenManagerInterface $csrfTokenManager = null)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return BooleanField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $isRenderedAsSwitch = true === $field->getCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH);

        if ($isRenderedAsSwitch) {
            // this happens when processing fields not for a specific entity (e.g. when
            // rendering the filters from the 'index' page)
            if (null === $entityDto->getPrimaryKeyValue()) {
                $field->setCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH, false);

                return;
            }

            $crudDto = $context->getCrud();

            if (null !== $crudDto && Action::NEW !== $crudDto->getCurrentAction()) {
                $toggleUrl = $this->adminUrlGenerator
                    ->setController($crudDto->getControllerFqcn())
                    ->setAction(Action::EDIT)
                    ->setEntityId($entityDto->getPrimaryKeyValue())
                    ->set('fieldName', $field->getProperty())
                    ->set('csrfToken', $this->csrfTokenManager?->getToken(BooleanField::CSRF_TOKEN_NAME))
                    ->generateUrl();
                $field->setCustomOption(BooleanField::OPTION_TOGGLE_URL, $toggleUrl);
            }

            $field->setFormTypeOptionIfNotSet('label_attr.class', 'checkbox-switch');
            $field->setCssClass($field->getCssClass().' has-switch');
        }
    }
}
