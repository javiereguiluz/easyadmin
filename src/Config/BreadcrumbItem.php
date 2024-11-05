<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final /* readonly */ class BreadcrumbItem
{
    public function __construct(
        public ?string $action = null,
        public string|\Closure|null $label = null,
        public ?string $route = null,
        public string|\Closure|null $crudControllerFqcn = null,
        public string|\Closure|null $entityId = null,
        public ?string $credentials = null,
        public string|\Closure|array|null $entityInstanceCallback = null,
    ) {
    }

    public function generateLabel(?object $entityInstance = null): ?string
    {
        if (null !== $this->entityInstanceCallback
            && null !== ($entityInstance = $this->getEntityInstance())
            && \is_callable([$entityInstance, $this->label])
        ) {
            return \call_user_func([$entityInstance, $this->label]);
        }

        return \is_callable($this->label) ? \call_user_func($this->label, $entityInstance) : $this->label;
    }

    public function generateCrudControllerFqcn(?object $entityInstance = null): ?string
    {
        if (null !== $this->entityInstanceCallback
            && null !== ($entityInstance = $this->getEntityInstance())
            && \is_callable([$entityInstance, $this->crudControllerFqcn])
        ) {
            return \call_user_func([$entityInstance, $this->crudControllerFqcn]);
        }

        return \is_callable($this->crudControllerFqcn) ? \call_user_func($this->crudControllerFqcn, $entityInstance) : $this->crudControllerFqcn;
    }

    public function generateEntityId(?object $entityInstance = null): ?string
    {
        if (null !== $this->entityInstanceCallback
            && null !== ($entityInstance = $this->getEntityInstance())
            && \is_callable([$entityInstance, $this->entityId])
        ) {
            return \call_user_func([$entityInstance, $this->entityId]);
        }

        return \is_callable($this->entityId) ? \call_user_func($this->entityId, $entityInstance) : $this->entityId;
    }

    private function getEntityInstance(): ?object
    {
        return null === $this->entityInstanceCallback || !\is_callable($this->entityInstanceCallback) ? null : \call_user_func($this->entityInstanceCallback);
    }
}
