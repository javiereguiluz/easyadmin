<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Context\AdminContextInterface;

/**
 * Inject this in services that need to get the admin context object.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
interface AdminContextProviderInterface extends AdminContextInterface
{
    public function hasContext(): bool;

    public function getContext(bool $throw = false): ?AdminContextInterface;
}
