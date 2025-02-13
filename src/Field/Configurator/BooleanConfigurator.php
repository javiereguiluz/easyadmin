<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class BooleanConfigurator implements FieldConfiguratorInterface
{
    public function __construct(
        private readonly AdminUrlGeneratorInterface $adminUrlGenerator,
        private readonly AuthorizationCheckerInterface $authChecker,
        private readonly ?CsrfTokenManagerInterface $csrfTokenManager = null,
    ) {
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return BooleanField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $isRenderedAsSwitch = true === $field->getCustomOption(BooleanField::OPTION_RENDER_AS_SWITCH);

        if ($isRenderedAsSwitch) {
            $crudDto = $context->getCrud();

            $hasEditPermission = $this->authChecker->isGranted(Permission::EA_EXECUTE_ACTION, ['action' => Action::EDIT, 'entity' => $entityDto]);

            if ($hasEditPermission && null !== $crudDto && null !== $entityDto->getPrimaryKeyValue()) {
                $toggleUrl = $this->adminUrlGenerator
                    ->setController($crudDto->getControllerFqcn())
                    ->setAction(Action::EDIT)
                    ->setEntityId($entityDto->getPrimaryKeyValue())
                    ->set('fieldName', $field->getProperty())
                    ->set('csrfToken', $this->csrfTokenManager?->getToken(BooleanField::CSRF_TOKEN_NAME))
                    ->generateUrl();
                $field->setCustomOption(BooleanField::OPTION_TOGGLE_URL, $toggleUrl);
            }

            if (!$hasEditPermission && Action::INDEX === $crudDto->getCurrentAction()) {
                $field->setFormTypeOptionIfNotSet('disabled', true);
            }

            $field->setFormTypeOptionIfNotSet('label_attr.class', 'checkbox-switch');
            $field->setCssClass($field->getCssClass().' has-switch');
        }
    }
}
