<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer;

use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\ActionNormalizer;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\DefaultNormalizer;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\EntityNormalizer;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\FormViewNormalizer;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\PropertyNormalizer;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\TemplateNormalizer;
use JavierEguiluz\Bundle\EasyAdminBundle\Configuration\Normalizer\ViewNormalizer;

/**
 * It applies several configuration normalizers in a row to transform any
 * inpput backend configuration into the normalized configuration used by
 * the rest of the code.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class ConfigurationNormalizer implements NormalizerInterface
{
    private $kernelRootDir;

    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    public function normalize(array $backendConfiguration)
    {
        $entityNormalizer = new EntityNormalizer();
        $formViewNormalizer = new FormViewNormalizer();
        $viewNormalizer = new ViewNormalizer();
        $propertyNormalizer = new PropertyNormalizer();
        $actionNormalizer = new ActionNormalizer();
        $templateNormalizer = new TemplateNormalizer($this->kernelRootDir);
        $defaultNormalizer = new DefaultNormalizer();

        $backendConfiguration = $entityNormalizer->normalize($backendConfiguration);
        $backendConfiguration = $formViewNormalizer->normalize($backendConfiguration);
        $backendConfiguration = $viewNormalizer->normalize($backendConfiguration);
        $backendConfiguration = $propertyNormalizer->normalize($backendConfiguration);
        $backendConfiguration = $actionNormalizer->normalize($backendConfiguration);
        $backendConfiguration = $templateNormalizer->normalize($backendConfiguration);
        $backendConfiguration = $defaultNormalizer->normalize($backendConfiguration);

        return $backendConfiguration;
    }
}
