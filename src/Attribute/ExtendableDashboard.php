<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ExtendableDashboard
{
    /**
     * Fixes admin route name duplication. Add to any Dashboard Controllers that you want to be able to extend from.
     *
     * This might be a bit of a hack.
     *
     * @param bool|null $hasExtraRoutes -- Set true on child controllers if there are routes which were not referenced by the parent controller
     */
    public function __construct(public ?bool $hasExtraRoutes = false)
    {
    }

    public function hasExtraRoutes(): ?bool
    {
        return $this->hasExtraRoutes;
    }
}
