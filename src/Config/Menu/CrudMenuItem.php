<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SortOrder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @see EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToCrud()
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CrudMenuItem implements MenuItemInterface
{
    use MenuItemTrait;

    public function __construct(TranslatableInterface|string $label, ?string $icon, string $entityFqcn)
    {
        $this->dto = new MenuItemDto();

        $this->dto->setType(MenuItemDto::TYPE_CRUD);
        $this->dto->setLabel($label);
        $this->dto->setIcon($icon);
        $this->dto->setRouteParameters([
            EA::CRUD_ACTION => 'index',
            EA::CRUD_CONTROLLER_FQCN => null,
            EA::ENTITY_FQCN => $entityFqcn,
            EA::ENTITY_ID => null,
        ]);
    }

    public function setHtmlAttribute(string $name, mixed $value): self
    {
        $this->dto->setHtmlAttribute($name, $value);

        return $this;
    }

    public function setController(string $controllerFqcn): self
    {
        $this->dto->setRouteParameters(array_merge(
            $this->dto->getRouteParameters(),
            [EA::CRUD_CONTROLLER_FQCN => $controllerFqcn]
        ));

        return $this;
    }

    public function setAction(string $actionName): self
    {
        $this->dto->setRouteParameters(array_merge(
            $this->dto->getRouteParameters(),
            [EA::CRUD_ACTION => $actionName]
        ));

        return $this;
    }

    public function setEntityId(AbstractUid|int|string $entityId): self
    {
        $this->dto->setRouteParameters(array_merge(
            $this->dto->getRouteParameters(),
            [EA::ENTITY_ID => $entityId]
        ));

        return $this;
    }

    /**
     * @param array $sortFieldsAndOrder ['fieldName' => 'ASC|DESC', ...]
     */
    public function setDefaultSort(array $sortFieldsAndOrder): self
    {
        $sortFieldsAndOrder = array_map('strtoupper', $sortFieldsAndOrder);
        foreach ($sortFieldsAndOrder as $sortField => $sortOrder) {
            if (!\in_array($sortOrder, [SortOrder::ASC, SortOrder::DESC], true)) {
                throw new \InvalidArgumentException(sprintf('The sort order can be only "ASC" or "DESC", "%s" given.', $sortOrder));
            }

            if (!\is_string($sortField)) {
                throw new \InvalidArgumentException(sprintf('The keys of the array that defines the default sort must be strings with the field names, but the given "%s" value is a "%s".', $sortField, \gettype($sortField)));
            }
        }

        $this->dto->setRouteParameters(array_merge(
            $this->dto->getRouteParameters(),
            [EA::SORT => $sortFieldsAndOrder]
        ));

        return $this;
    }
}
