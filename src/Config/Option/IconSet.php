<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Config\Option;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class IconSet
{
    public const FontAwesome = 'fontAwesome';
    // Use this when you want to display icons from any set different from the built-in one
    // (the values you pass as the icon names will be used "as is" in the default ux_icon() function)
    public const Custom = 'custom';
    // 'Internal' set is reserved for the icons of the built-in EasyAdmin UI; don't use it in your apps
    public const Internal = 'internal';
}
