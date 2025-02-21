<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Factory;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\Proxy;
use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\EntityCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionConfigDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuiltEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityNotFoundException;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityFactory
{
    private FieldFactory $fieldFactory;
    private ActionFactory $actionFactory;
    private AuthorizationCheckerInterface $authorizationChecker;
    private ManagerRegistry $doctrine;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(FieldFactory $fieldFactory, ActionFactory $actionFactory, AuthorizationCheckerInterface $authorizationChecker, ManagerRegistry $doctrine, EventDispatcherInterface $eventDispatcher)
    {
        $this->fieldFactory = $fieldFactory;
        $this->actionFactory = $actionFactory;
        $this->authorizationChecker = $authorizationChecker;
        $this->doctrine = $doctrine;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function processFields(EntityDto $entityDto, FieldCollection $fields): void
    {
        $this->fieldFactory->processFields($entityDto, $fields);
    }

    public function processFieldsForAll(EntityCollection $entities, FieldCollection $fields): void
    {
        foreach ($entities as $entity) {
            $this->processFields($entity, clone $fields);
            $entities->set($entity);
        }
    }

    public function processActions(EntityDto $entityDto, ActionConfigDto $actionConfigDto): void
    {
        if (!$entityDto->isAccessible()) {
            return;
        }
        $this->actionFactory->processEntityActions($entityDto, $actionConfigDto);
    }

    public function processActionsForAll(EntityCollection $entities, ActionConfigDto $actionConfigDto): ActionCollection
    {
        foreach ($entities as $entity) {
            $this->processActions($entity, clone $actionConfigDto);
        }

        return $this->actionFactory->processGlobalActions($actionConfigDto);
    }

    public function create(string $entityFqcn, $entityId = null, string|Expression|null $entityPermission = null): EntityDto
    {
        return $this->doCreate($entityFqcn, $entityId, $entityPermission);
    }

    public function createForEntityInstance($entityInstance): EntityDto
    {
        return $this->doCreate(null, null, null, $entityInstance);
    }

    public function createCollection(EntityDto $entityDto, ?iterable $entityInstances): EntityCollection
    {
        $entityDtos = [];

        foreach ($entityInstances as $entityInstance) {
            $newEntityDto = $entityDto->newWithInstance($entityInstance);
            $newEntityId = $newEntityDto->getPrimaryKeyValueAsString();
            if (!$this->authorizationChecker->isGranted(Permission::EA_ACCESS_ENTITY, $newEntityDto)) {
                $newEntityDto->markAsInaccessible();
            }

            $entityDtos[$newEntityId] = $newEntityDto;
        }

        return EntityCollection::new($entityDtos);
    }

    public function getEntityMetadata(string $entityFqcn): ClassMetadata
    {
        $entityManager = $this->getEntityManager($entityFqcn);
        /** @var ClassMetadata $entityMetadata */
        $entityMetadata = $entityManager->getClassMetadata($entityFqcn);

        if (1 !== \count($entityMetadata->getIdentifierFieldNames())) {
            throw new \RuntimeException(sprintf('EasyAdmin does not support Doctrine entities with composite primary keys (such as the ones used in the "%s" entity).', $entityFqcn));
        }

        return $entityMetadata;
    }

    private function doCreate(?string $entityFqcn = null, $entityId = null, string|Expression|null $entityPermission = null, $entityInstance = null): EntityDto
    {
        if (null === $entityInstance && null !== $entityFqcn) {
            $entityInstance = null === $entityId ? null : $this->getEntityInstance($entityFqcn, $entityId);
        }

        if (null !== $entityInstance && null === $entityFqcn) {
            if ($entityInstance instanceof Proxy) {
                $entityInstance->__load();
            }

            $entityFqcn = $this->getRealClass($entityInstance::class);
        }

        $entityMetadata = $this->getEntityMetadata($entityFqcn);
        $entityDto = new EntityDto($entityFqcn, $entityMetadata, $entityPermission, $entityInstance);

        if (!$this->authorizationChecker->isGranted(Permission::EA_ACCESS_ENTITY, $entityDto)) {
            $entityDto->markAsInaccessible();
        }

        $this->eventDispatcher->dispatch(new AfterEntityBuiltEvent($entityDto));

        return $entityDto;
    }

    private function getEntityManager(string $entityFqcn): ObjectManager
    {
        if (null === $entityManager = $this->doctrine->getManagerForClass($entityFqcn)) {
            throw new \RuntimeException(sprintf('There is no Doctrine Entity Manager defined for the "%s" class', $entityFqcn));
        }

        return $entityManager;
    }

    private function getEntityInstance(string $entityFqcn, $entityIdValue): object
    {
        $entityManager = $this->getEntityManager($entityFqcn);
        if (null === $entityInstance = $entityManager->getRepository($entityFqcn)->find($entityIdValue)) {
            $entityIdName = $entityManager->getClassMetadata($entityFqcn)->getIdentifierFieldNames()[0];

            throw new EntityNotFoundException(['entity_name' => $entityFqcn, 'entity_id_name' => $entityIdName, 'entity_id_value' => $entityIdValue]);
        }

        return $entityInstance;
    }

    /**
     * Code copied from Symfony\Bridge\Doctrine\Form\DoctrineOrmTypeGuesser
     * because Doctrine ORM 3.x removed the ClassUtil class where this method was defined
     * (c) Fabien Potencier <fabien@symfony.com> - MIT License.
     */
    private function getRealClass(string $class): string
    {
        if (false === $pos = strrpos($class, '\\'.Proxy::MARKER.'\\')) {
            return $class;
        }

        return substr($class, $pos + Proxy::MARKER_LENGTH + 2);
    }
}
