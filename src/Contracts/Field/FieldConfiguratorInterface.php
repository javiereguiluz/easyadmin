<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Field;

use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
interface FieldConfiguratorInterface
{
    public function supports(FieldDto $field, EntityDto $entityDto): bool;

    /**
     * @param AdminContext $context This will change to AdminContextInterface in the next major version
     */
    public function configure(FieldDto $field, EntityDto $entityDto, AdminContext $context): void;
}
