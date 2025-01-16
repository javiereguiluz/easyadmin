<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Action;

use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;

/**
 * @author Eric Abouaf <eric.abouaf@gmail.com>
 */
interface ActionInterface
{
    public function getAsDto(): ActionDto;

    public function __toString();
}
