<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Form\Type\ComparisonType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Serg N. Kalachev <serg@kalachev.ru>
 */
final class ScopeFilterDto
{
    public function __construct(
        private string $propertyName,
        private string|array|null $value = null,
        private string $comparison = ComparisonType::EQ,
        private ?string $value2 = null,
        private ?bool $unset = false
    ) {
        if (!\in_array($comparison, (new \ReflectionClass(ComparisonType::class))->getConstants(), true)) {
            throw new \InvalidArgumentException(sprintf('Comparison "%s" is not supported.', $comparison));
        }
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function updateRequest(Request $request): void
    {
        $filters = $request->query->all()['filters'] ?? [];
        if ($this->unset) {
            unset($filters[$this->propertyName]);
        } else {
            if (!isset($filters[$this->propertyName])) {
                $filters[$this->propertyName] = [];
            }
            $filters[$this->propertyName]['comparison'] = $this->comparison;
            $filters[$this->propertyName]['value'] = $this->value;
            if (null !== $this->value2) {
                $filters[$this->propertyName]['value2'] = $this->value2;
            }
        }
        $request->query->set('filters', $filters);
    }
}
