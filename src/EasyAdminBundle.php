<?php

namespace EasyCorp\Bundle\EasyAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class EasyAdminBundle extends Bundle
{
    public const VERSION = '5.0.0-DEV';

    public function getPath(): string
    {
        $reflected = new \ReflectionObject($this);

        return \dirname($reflected->getFileName(), 2);
    }
}
