<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\FieldMapping;
use Doctrine\ORM\Mapping\ManyToManyAssociationMapping;
use Doctrine\ORM\Mapping\ManyToOneAssociationMapping;
use Doctrine\ORM\Mapping\OneToManyAssociationMapping;
use Doctrine\ORM\Mapping\OneToOneAssociationMapping;
use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityDto
{
    private bool $isAccessible = true;
    private string $fqcn;
    private ClassMetadata $metadata;
    private $instance;
    private $primaryKeyName;
    private mixed $primaryKeyValue = null;
    private string|Expression|null $permission;
    private ?FieldCollection $fields = null;
    private ?ActionCollection $actions = null;

    public function __construct(string $entityFqcn, ClassMetadata $entityMetadata, string|Expression|null $entityPermission = null, /* ?object */ $entityInstance = null)
    {
        if (!\is_object($entityInstance)
            && null !== $entityInstance) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$entityInstance',
                __METHOD__,
                '"object" or "null"',
                \gettype($entityInstance)
            );
        }

        $this->fqcn = $entityFqcn;
        $this->metadata = $entityMetadata;
        $this->instance = $entityInstance;
        $this->primaryKeyName = $this->metadata->getIdentifierFieldNames()[0];
        $this->permission = $entityPermission;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function getName(): string
    {
        return basename(str_replace('\\', '/', $this->fqcn));
    }

    public function toString(): string
    {
        if (null === $this->instance) {
            return '';
        }

        if (method_exists($this->instance, '__toString')) {
            return (string) $this->instance;
        }

        return sprintf('%s #%s', $this->getName(), substr($this->getPrimaryKeyValueAsString(), 0, 16));
    }

    public function getInstance()/* : ?object */
    {
        return $this->instance;
    }

    public function getPrimaryKeyName(): ?string
    {
        return $this->primaryKeyName;
    }

    public function getPrimaryKeyValue(): mixed
    {
        if (null === $this->instance) {
            return null;
        }

        if (null !== $this->primaryKeyValue) {
            return $this->primaryKeyValue;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        try {
            $primaryKeyValue = $propertyAccessor->getValue($this->instance, $this->primaryKeyName);
        } catch (UninitializedPropertyException $exception) {
            $primaryKeyValue = null;
        }

        return $this->primaryKeyValue = $primaryKeyValue;
    }

    public function getPrimaryKeyValueAsString(): string
    {
        return (string) $this->getPrimaryKeyValue();
    }

    public function getPermission(): string|Expression|null
    {
        return $this->permission;
    }

    public function isAccessible(): bool
    {
        return $this->isAccessible;
    }

    public function markAsInaccessible(): void
    {
        $this->isAccessible = false;
        $this->instance = null;
        $this->fields = null;
    }

    public function getFields(): ?FieldCollection
    {
        return $this->fields;
    }

    public function setFields(FieldCollection $fields): void
    {
        $this->fields = $fields;
    }

    public function setActions(ActionCollection $actions): void
    {
        $this->actions = $actions;
    }

    public function getActions(): ActionCollection
    {
        return $this->actions;
    }

    /**
     * Returns the names of all properties defined in the entity, no matter
     * if they are used or not in the application.
     */
    public function getAllPropertyNames(): array
    {
        return $this->metadata->getFieldNames();
    }

    public function getPropertyMetadata(string $propertyName): KeyValueStore
    {
        if (\array_key_exists($propertyName, $this->metadata->fieldMappings)) {
            /** @var FieldMapping|array $fieldMapping */
            $fieldMapping = $this->metadata->fieldMappings[$propertyName];
            // Doctrine ORM 2.x returns an array and Doctrine ORM 3.x returns a FieldMapping object
            if ($fieldMapping instanceof FieldMapping) {
                $fieldMapping = (array) $fieldMapping;
            }

            return KeyValueStore::new($fieldMapping);
        }

        if (\array_key_exists($propertyName, $this->metadata->associationMappings)) {
            /** @var OneToOneAssociationMapping|OneToManyAssociationMapping|ManyToOneAssociationMapping|ManyToManyAssociationMapping|array $associationMapping */
            $associationMapping = $this->metadata->associationMappings[$propertyName];
            // Doctrine ORM 2.x returns an array and Doctrine ORM 3.x returns one of the many *Mapping objects
            // there's not a single interface implemented by all of them, so let's only check if it's an object
            if (\is_object($associationMapping)) {
                // Doctrine ORM 3.x doesn't include the 'type' key that tells the type of association
                // recreate that key to keep the code compatible with both versions
                $associationType = match (true) {
                    $associationMapping instanceof OneToOneAssociationMapping => ClassMetadata::ONE_TO_ONE,
                    $associationMapping instanceof OneToManyAssociationMapping => ClassMetadata::ONE_TO_MANY,
                    $associationMapping instanceof ManyToOneAssociationMapping => ClassMetadata::MANY_TO_ONE,
                    $associationMapping instanceof ManyToManyAssociationMapping => ClassMetadata::MANY_TO_MANY,
                    default => null,
                };

                $associationMapping = (array) $associationMapping;
                $associationMapping['type'] = $associationType;
            }

            return KeyValueStore::new($associationMapping);
        }

        throw new \InvalidArgumentException(sprintf('The "%s" field does not exist in the "%s" entity.', $propertyName, $this->getFqcn()));
    }

    public function getPropertyDataType(string $propertyName)
    {
        return $this->getPropertyMetadata($propertyName)->get('type');
    }

    public function hasProperty(string $propertyName): bool
    {
        return \array_key_exists($propertyName, $this->metadata->fieldMappings)
            || \array_key_exists($propertyName, $this->metadata->associationMappings);
    }

    public function isAssociation(string $propertyName): bool
    {
        return \array_key_exists($propertyName, $this->metadata->associationMappings)
            || (str_contains($propertyName, '.') && !$this->isEmbeddedClassProperty($propertyName));
    }

    public function isToOneAssociation(string $propertyName): bool
    {
        $associationType = $this->getPropertyMetadata($propertyName)->get('type');

        return \in_array($associationType, [ClassMetadata::ONE_TO_ONE, ClassMetadata::MANY_TO_ONE], true);
    }

    public function isToManyAssociation(string $propertyName): bool
    {
        $associationType = $this->getPropertyMetadata($propertyName)->get('type');

        return \in_array($associationType, [ClassMetadata::ONE_TO_MANY, ClassMetadata::MANY_TO_MANY], true);
    }

    public function isEmbeddedClassProperty(string $propertyName): bool
    {
        $propertyNameParts = explode('.', $propertyName, 2);

        return \array_key_exists($propertyNameParts[0], $this->metadata->embeddedClasses);
    }

    public function setInstance(?object $newEntityInstance): void
    {
        if (null !== $this->instance && null !== $newEntityInstance && !$newEntityInstance instanceof $this->fqcn) {
            throw new \InvalidArgumentException(sprintf('The new entity instance must be of the same type as the previous instance (original instance: "%s", new instance: "%s").', $this->fqcn, $newEntityInstance::class));
        }

        $this->instance = $newEntityInstance;
        $this->primaryKeyValue = null;
    }

    public function newWithInstance(/* object */ $newEntityInstance): self
    {
        if (!\is_object($newEntityInstance)) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$newEntityInstance',
                __METHOD__,
                '"object"',
                \gettype($newEntityInstance)
            );
        }

        if (null !== $this->instance && !$newEntityInstance instanceof $this->fqcn) {
            throw new \InvalidArgumentException(sprintf('The new entity instance must be of the same type as the previous instance (original instance: "%s", new instance: "%s").', $this->fqcn, $newEntityInstance::class));
        }

        return new self($this->fqcn, $this->metadata, $this->permission, $newEntityInstance);
    }
}
