<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Twig\Component;

use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Exception\MissingResourceException;

class Flag
{
    public string $countryCode;
    public int $height = 17;

    public function getCountryName(): string
    {
        try {
            return Countries::getName($this->countryCode);
        } catch (MissingResourceException) {
            return '';
        }
    }

    public function getFlagAsSvg(): string
    {
        $flagSvgFilePath = sprintf('%s/../../../assets/icons/flags/%s.svg', __DIR__, $this->countryCode);
        if (!file_exists($flagSvgFilePath)) {
            return sprintf('<svg xmlns="http://www.w3.org/2000/svg" class="country-flag" height="%d" viewBox="0 0 25 17"><title>You are not seeing a country flag here because the "%s.svg" file associated to given the "%s" country code does not exist in the assets/icons/flags/ directory of EasyAdmin.</title><rect width="100%%" height="%d" fill="#ff0000"/></svg>', $this->height, $this->countryCode, $this->countryCode, $this->height);
        }

        return str_replace(['__HEIGHT__', '__COUNTRY_NAME__'], [$this->height, $this->getCountryName()], file_get_contents($flagSvgFilePath));
    }
}
