<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Inspector;

use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Context\AdminContextInterface;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector as BaseDataCollector;

/**
 * Collects information about the requests related to EasyAdmin and displays
 * it both in the web debug toolbar and in the profiler.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class DataCollector extends BaseDataCollector
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
    ) {
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function collect(Request $request, Response $response, $exception = null): void
    {
        if (null === $context = $this->adminContextProvider->getContext()) {
            return;
        }

        $collectedData = [];
        foreach ($this->collectData($context) as $key => $value) {
            $collectedData[$key] = $this->cloneVar($value);
        }

        $this->data = $collectedData;
    }

    public function isEasyAdminRequest(): bool
    {
        return 0 !== \count($this->data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    private function collectData(AdminContextInterface $context): array
    {
        return [
            'CRUD Controller FQCN' => null === $context->getCrud() ? null : $context->getCrud()->getControllerFqcn(),
            'CRUD Action' => $context->getRequest()->get(EA::CRUD_ACTION),
            'Entity ID' => $context->getRequest()->get(EA::ENTITY_ID),
            'Sort' => $context->getRequest()->get(EA::SORT),
        ];
    }

    public function getName(): string
    {
        return 'easyadmin';
    }
}
