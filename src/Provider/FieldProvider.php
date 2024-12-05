<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Provider;

use Doctrine\DBAL\Types\Types;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class FieldProvider
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
    ) {
    }

    public function getDefaultFields(string $pageName): array
    {
        $defaultPropertyNames = [];
        $maxNumProperties = Crud::PAGE_INDEX === $pageName ? 7 : \PHP_INT_MAX;
        $entityDto = $this->adminContextProvider->getContext()->getEntity();

        // don't use Types::OBJECT because it was removed in Doctrine ORM 3.0
        $excludedPropertyTypes = [
            Crud::PAGE_EDIT => [Types::BINARY, Types::BLOB, Types::JSON, 'object'],
            Crud::PAGE_INDEX => [Types::BINARY, Types::BLOB, Types::GUID, Types::JSON, 'object', Types::TEXT],
            Crud::PAGE_NEW => [Types::BINARY, Types::BLOB, Types::JSON, 'object'],
            Crud::PAGE_DETAIL => [Types::BINARY, Types::JSON, 'object'],
        ];

        $excludedPropertyNames = [
            Crud::PAGE_EDIT => [$entityDto->getPrimaryKeyName()],
            Crud::PAGE_INDEX => ['password', 'salt', 'slug', 'updatedAt', 'uuid'],
            Crud::PAGE_NEW => [$entityDto->getPrimaryKeyName()],
            Crud::PAGE_DETAIL => [],
        ];

        foreach ($entityDto->getAllPropertyNames() as $propertyName) {
            $metadata = $entityDto->getPropertyMetadata($propertyName);
            if (!\in_array($propertyName, $excludedPropertyNames[$pageName], true) && !\in_array($metadata->get('type'), $excludedPropertyTypes[$pageName], true)) {
                $defaultPropertyNames[] = $propertyName;
            }
        }

        if (\count($defaultPropertyNames) > $maxNumProperties) {
            $defaultPropertyNames = \array_slice($defaultPropertyNames, 0, $maxNumProperties, true);
        }

        return array_map(static fn (string $fieldName) => Field::new($fieldName), $defaultPropertyNames);
    }
}
