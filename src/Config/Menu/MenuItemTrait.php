<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config\Menu;

use EasyCorp\Bundle\EasyAdminBundle\Dto\MenuItemDto;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
trait MenuItemTrait
{
    private MenuItemDto $dto;

    public function setCssClass(string $cssClass): self
    {
        $this->dto->setCssClass($cssClass);

        return $this;
    }

    public function setQueryParameter(string $parameterName, mixed $parameterValue): self
    {
        $this->dto->setRouteParameter($parameterName, $parameterValue);

        return $this;
    }

    public function setPermission(string|Expression $permission): self
    {
        $this->dto->setPermission($permission);

        return $this;
    }

    public function setTranslationParameters(array $parameters): self
    {
        $this->dto->setTranslationParameters($parameters);

        return $this;
    }

    public function setLinkRel(string $rel): self
    {
        $this->dto->setLinkRel($rel);

        return $this;
    }

    public function setLinkTarget(string $target): self
    {
        $this->dto->setLinkTarget($target);

        return $this;
    }

    /**
     * @param \Stringable|string|int|float|bool|null $content This is rendered as the value of the badge; it can be anything that can be cast to
     *                                                        a string (numbers, stringable objects, etc.)
     * @param string                                 $style   Pass one of these values for predefined styles: 'primary', 'secondary', 'success',
     *                                                        'danger', 'warning', 'info', 'light', 'dark'
     *                                                        Otherwise, the passed value is applied "as is" to the `style` attribute of the HTML
     *                                                        element of the badge
     */
    public function setBadge(\Stringable|string|int|float|bool|null $content, string $style = 'secondary', array $htmlAttributes = []): self
    {
        $this->dto->setBadge($content, $style, $htmlAttributes);

        return $this;
    }

    public function getAsDto(): MenuItemDto
    {
        return $this->dto;
    }
}
