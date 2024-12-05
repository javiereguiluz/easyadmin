<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Tests\Config;

use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\IconSet;
use PHPUnit\Framework\TestCase;

class AssetsTest extends TestCase
{
    public function testDefaultIconSet()
    {
        $assetsConfig = Assets::new();

        $this->assertSame(IconSet::FontAwesome, $assetsConfig->getAsDto()->getIconSet());
        $this->assertSame('', $assetsConfig->getAsDto()->getDefaultIconPrefix());
    }

    public function testCustomIconSet()
    {
        $assetsConfig = Assets::new();
        $assetsConfig->useCustomIconSet();

        $this->assertSame(IconSet::Custom, $assetsConfig->getAsDto()->getIconSet());
        $this->assertSame('', $assetsConfig->getAsDto()->getDefaultIconPrefix());
    }

    public function testCustomIconSetWithDefaultPrefix()
    {
        $assetsConfig = Assets::new();
        $assetsConfig->useCustomIconSet('some-prefix');

        $this->assertSame(IconSet::Custom, $assetsConfig->getAsDto()->getIconSet());
        $this->assertSame('some-prefix', $assetsConfig->getAsDto()->getDefaultIconPrefix());
    }
}
