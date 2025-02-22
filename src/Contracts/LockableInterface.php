<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Contracts;

/**
 * Class LockableInterface.
 */
interface LockableInterface
{
    /**
     * Returns the version or timestamp used to manage locking.
     *
     * @return ?int A version number or timestamp
     */
    public function getLockVersion(): ?int;
}
