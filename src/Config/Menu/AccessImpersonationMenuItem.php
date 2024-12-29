<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @see EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem::linkToAccessImpersonation()
 *
 * @author Elías Fernández <eliasfernandez@gmail.com>
 */
final class AccessImpersonationMenuItem implements MenuItemInterface
{
    use MenuItemTrait;

    public function __construct(TranslatableInterface|string $label, ?string $icon)
    {
        $this->dto = new MenuItemDto();

        $this->dto->setType(MenuItemDto::TYPE_ACCESS_IMPERSONATION);
        $this->dto->setLabel($label);
        $this->dto->setIcon($icon);
        $this->dto->setLinkUrl('test');
    }
}
