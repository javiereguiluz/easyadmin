<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Field\Configurator;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\PersistentCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\TextAlign;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldConfiguratorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Factory\ControllerFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\EntityFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudAutocompleteType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\CrudFormType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use function Symfony\Component\Translation\t;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class AssociationConfigurator implements FieldConfiguratorInterface
{
    private EntityFactory $entityFactory;
    private AdminUrlGeneratorInterface $adminUrlGenerator;
    private RequestStack $requestStack;
    private ControllerFactory $controllerFactory;

    public function __construct(EntityFactory $entityFactory, AdminUrlGeneratorInterface $adminUrlGenerator, RequestStack $requestStack, ControllerFactory $controllerFactory)
    {
        $this->entityFactory = $entityFactory;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->requestStack = $requestStack;
        $this->controllerFactory = $controllerFactory;
    }

    public function supports(FieldDto $field, EntityDto $entityDto): bool
    {
        return AssociationField::class === $field->getFieldFqcn();
    }

    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void
    {
        $propertyName = $field->getProperty();
        if (!$entityDto->isAssociation($propertyName)) {
            throw new \RuntimeException(sprintf('The "%s" field is not a Doctrine association, so it cannot be used as an association field.', $propertyName));
        }

        $targetEntityFqcn = $field->getDoctrineMetadata()->get('targetEntity');
        // the target CRUD controller can be NULL; in that case, field value doesn't link to the related entity
        $targetCrudControllerFqcn = $field->getCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_CONTROLLER)
            ?? $context->getCrudControllers()->findCrudFqcnByEntityFqcn($targetEntityFqcn);

        if (true === $field->getCustomOption(AssociationField::OPTION_RENDER_AS_EMBEDDED_FORM)) {
            if (false === $entityDto->isToOneAssociation($propertyName)) {
                throw new \RuntimeException(
                    sprintf(
                        'The "%s" association field of "%s" is a to-many association but it\'s trying to use the "renderAsEmbeddedForm()" option, which is only available for to-one associations. If you want to use a CRUD form to render to-many associations, use a CollectionField instead of the AssociationField.',
                        $field->getProperty(),
                        $context->getCrud()?->getControllerFqcn(),
                    )
                );
            }

            if (null === $targetCrudControllerFqcn) {
                throw new \RuntimeException(
                    sprintf(
                        'The "%s" association field of "%s" wants to render its contents using an EasyAdmin CRUD form. However, no CRUD form was found related to this field. You can either create a CRUD controller for the entity "%s" or pass the CRUD controller to use as the first argument of the "renderAsEmbeddedForm()" method.',
                        $field->getProperty(),
                        $context->getCrud()?->getControllerFqcn(),
                        $targetEntityFqcn
                    )
                );
            }

            $this->configureCrudForm($field, $entityDto, $propertyName, $targetEntityFqcn, $targetCrudControllerFqcn);

            return;
        }

        $field->setCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_CONTROLLER, $targetCrudControllerFqcn);

        if (AssociationField::WIDGET_AUTOCOMPLETE === $field->getCustomOption(AssociationField::OPTION_WIDGET)) {
            $field->setFormTypeOption('attr.data-ea-widget', 'ea-autocomplete');
        }

        $field->setFormTypeOption('attr.data-ea-autocomplete-render-items-as-html', true === $field->getCustomOption(AssociationField::OPTION_ESCAPE_HTML_CONTENTS) ? 'false' : 'true');

        // check for embedded associations
        $propertyNameParts = explode('.', $propertyName);
        if (\count($propertyNameParts) > 1) {
            // prepare starting class for association
            $targetEntityFqcn = $entityDto->getPropertyMetadata($propertyNameParts[0])->get('targetEntity');
            array_shift($propertyNameParts);
            $metadata = $this->entityFactory->getEntityMetadata($targetEntityFqcn);

            foreach ($propertyNameParts as $association) {
                if (!$metadata->hasAssociation($association)) {
                    throw new \RuntimeException(sprintf('There is no association for the class "%s" with name "%s"', $targetEntityFqcn, $association));
                }

                // overwrite next class from association
                $targetEntityFqcn = $metadata->getAssociationTargetClass($association);

                // read next association metadata
                $metadata = $this->entityFactory->getEntityMetadata($targetEntityFqcn);
            }

            $accessor = new PropertyAccessor();
            $targetCrudControllerFqcn = $field->getCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_CONTROLLER);

            $field->setFormTypeOptionIfNotSet('class', $targetEntityFqcn);

            try {
                if (null !== $entityDto->getInstance()) {
                    $relatedEntityId = $accessor->getValue($entityDto->getInstance(), $propertyName.'.'.$metadata->getIdentifierFieldNames()[0]);
                    $relatedEntityDto = $this->entityFactory->create($targetEntityFqcn, $relatedEntityId);

                    $field->setCustomOption(AssociationField::OPTION_RELATED_URL, $this->generateLinkToAssociatedEntity($targetCrudControllerFqcn, $relatedEntityDto));
                    $field->setFormattedValue($this->formatAsString($relatedEntityDto->getInstance(), $relatedEntityDto));
                }
            } catch (UnexpectedTypeException|RouteNotFoundException) {
                // this may throw an exception if:
                //   * something in the tree is null; do nothing in that case;
                //   * the route is not found, which happens when the associated entity is not accessible from this dashboard; do nothing in that case either.
            }
        } else {
            if ($entityDto->isToOneAssociation($propertyName)) {
                $this->configureToOneAssociation($field);
            }

            if ($entityDto->isToManyAssociation($propertyName)) {
                $this->configureToManyAssociation($field);
            }
        }

        if (true === $field->getCustomOption(AssociationField::OPTION_AUTOCOMPLETE)) {
            $targetCrudControllerFqcn = $field->getCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_CONTROLLER);
            if (null === $targetCrudControllerFqcn) {
                throw new \RuntimeException(sprintf('The "%s" field cannot be autocompleted because it doesn\'t define the related CRUD controller FQCN with the "setCrudController()" method.', $field->getProperty()));
            }

            $field->setFormType(CrudAutocompleteType::class);

            try {
                $autocompleteEndpointUrl = $this->adminUrlGenerator
                    ->unsetAll()
                    ->set('page', 1) // The autocomplete should always start on the first page
                    ->setController($targetCrudControllerFqcn)
                    ->setAction('autocomplete')
                    ->set(AssociationField::PARAM_AUTOCOMPLETE_CONTEXT, [
                        // when using pretty URLs, the data is in the request attributes instead of the autocomplete context
                        EA::CRUD_CONTROLLER_FQCN => $context->getRequest()->attributes->get(EA::CRUD_CONTROLLER_FQCN) ?? $context->getRequest()->query->get(EA::CRUD_CONTROLLER_FQCN),
                        'propertyName' => $propertyName,
                        'originatingPage' => $context->getCrud()->getCurrentPage(),
                    ])
                    ->generateUrl();
            } catch (RouteNotFoundException $e) {
                // this may throw a "route not found" exception if the associated entity is not
                // accessible from this dashboard; do nothing in that case.
            }

            $field->setFormTypeOption('attr.data-ea-autocomplete-endpoint-url', $autocompleteEndpointUrl ?? null);
        } else {
            $field->setFormTypeOptionIfNotSet('query_builder', static function (EntityRepository $repository) use ($field) {
                // TODO: should this use `createIndexQueryBuilder` instead, so we get the default ordering etc.?
                // it would then be identical to the one used in autocomplete action, but it is a bit complex getting it in here
                $queryBuilder = $repository->createQueryBuilder('entity');
                if (null !== $queryBuilderCallable = $field->getCustomOption(AssociationField::OPTION_QUERY_BUILDER_CALLABLE)) {
                    $queryBuilderCallable($queryBuilder);
                }

                return $queryBuilder;
            });
        }
    }

    private function configureToOneAssociation(FieldDto $field): void
    {
        $field->setCustomOption(AssociationField::OPTION_DOCTRINE_ASSOCIATION_TYPE, 'toOne');

        if (false === $field->getFormTypeOption('required')) {
            $field->setFormTypeOptionIfNotSet('attr.placeholder', t('label.form.empty_value', [], 'EasyAdminBundle'));
        }

        $targetEntityFqcn = $field->getDoctrineMetadata()->get('targetEntity');
        $targetCrudControllerFqcn = $field->getCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_CONTROLLER);

        $targetEntityDto = null === $field->getValue()
            ? $this->entityFactory->create($targetEntityFqcn)
            : $this->entityFactory->createForEntityInstance($field->getValue());
        $field->setFormTypeOptionIfNotSet('class', $targetEntityDto->getFqcn());

        try {
            $field->setCustomOption(AssociationField::OPTION_RELATED_URL, $this->generateLinkToAssociatedEntity($targetCrudControllerFqcn, $targetEntityDto));
        } catch (RouteNotFoundException $e) {
            // this may throw a "route not found" exception if the associated entity is not
            // accessible from this dashboard; do nothing in that case.
        }

        $field->setFormattedValue($this->formatAsString($field->getValue(), $targetEntityDto));
    }

    private function configureToManyAssociation(FieldDto $field): void
    {
        $field->setCustomOption(AssociationField::OPTION_DOCTRINE_ASSOCIATION_TYPE, 'toMany');

        $field->setFormTypeOptionIfNotSet('multiple', true);

        /* @var PersistentCollection $collection */
        $field->setFormTypeOptionIfNotSet('class', $field->getDoctrineMetadata()->get('targetEntity'));

        if (null === $field->getTextAlign()) {
            $field->setTextAlign(TextAlign::RIGHT);
        }

        $field->setFormattedValue($this->countNumElements($field->getValue()));
    }

    private function formatAsString($entityInstance, EntityDto $entityDto): ?string
    {
        if (null === $entityInstance) {
            return null;
        }

        if (method_exists($entityInstance, '__toString')) {
            return (string) $entityInstance;
        }

        if (null !== $primaryKeyValue = $entityDto->getPrimaryKeyValue()) {
            return sprintf('%s #%s', $entityDto->getName(), $primaryKeyValue);
        }

        return $entityDto->getName();
    }

    private function generateLinkToAssociatedEntity(?string $crudController, EntityDto $entityDto): ?string
    {
        if (null === $crudController) {
            return null;
        }

        $primaryKeyValue = $entityDto->getPrimaryKeyValue();
        // when processing fields for an entity in the index page, the primary key of the
        // associated entity is null (e.g. admin_post_index and Post <-> User)
        $crudAction = null === $primaryKeyValue ? Action::INDEX : Action::DETAIL;

        // TODO: check if user has permission to see the related entity
        return $this->adminUrlGenerator
            ->setController($crudController)
            ->setAction($crudAction)
            ->setEntityId($primaryKeyValue)
            ->unset(EA::FILTERS)
            ->unset(EA::PAGE)
            ->unset(EA::QUERY)
            ->unset(EA::SORT)
            ->generateUrl();
    }

    private function countNumElements($collection): int
    {
        if (null === $collection) {
            return 0;
        }

        if (is_countable($collection)) {
            return \count($collection);
        }

        if ($collection instanceof \Traversable) {
            return iterator_count($collection);
        }

        return 0;
    }

    private function configureCrudForm(FieldDto $field, EntityDto $entityDto, string $propertyName, string $targetEntityFqcn, string $targetCrudControllerFqcn): void
    {
        $field->setFormType(CrudFormType::class);
        $propertyAccessor = new PropertyAccessor();

        if (null === $entityDto->getInstance()) {
            $associatedEntity = null;
        } else {
            $associatedEntity = $propertyAccessor->isReadable($entityDto->getInstance(), $propertyName)
                ? $propertyAccessor->getValue($entityDto->getInstance(), $propertyName)
                : null;
        }

        if (null === $associatedEntity) {
            $targetCrudControllerAction = Action::NEW;
            $targetCrudControllerPageName = $field->getCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_NEW_PAGE_NAME) ?? Crud::PAGE_NEW;
        } else {
            $targetCrudControllerAction = Action::EDIT;
            $targetCrudControllerPageName = $field->getCustomOption(AssociationField::OPTION_EMBEDDED_CRUD_FORM_EDIT_PAGE_NAME) ?? Crud::PAGE_EDIT;
        }

        $field->setFormTypeOption(
            'entityDto',
            $this->createEntityDto($targetEntityFqcn, $targetCrudControllerFqcn, $targetCrudControllerAction, $targetCrudControllerPageName),
        );
    }

    private function createEntityDto(string $entityFqcn, string $crudControllerFqcn, string $crudControllerAction, string $crudControllerPageName): EntityDto
    {
        $entityDto = $this->entityFactory->create($entityFqcn);

        $crudController = $this->controllerFactory->getCrudControllerInstance(
            $crudControllerFqcn,
            $crudControllerAction,
            $this->requestStack->getMainRequest()
        );

        $fields = $crudController->configureFields($crudControllerPageName);

        $this->entityFactory->processFields($entityDto, FieldCollection::new($fields));

        return $entityDto;
    }
}
