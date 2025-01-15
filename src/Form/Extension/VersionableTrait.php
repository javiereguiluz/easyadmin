<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Extension;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class VersionableTrait.
 *
 * @author Ahmed EBEN HASSINE <ahmedbhs123@gmail.com>
 */
trait VersionableTrait
{
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\Version]
    private ?int $lockVersion = null;

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }
}
