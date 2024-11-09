<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EmbedType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'ea_embedded_collection';
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
