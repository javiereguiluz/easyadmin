<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * The 'fieldset' form type is a special form type used to display a design
 * element needed to create complex form layouts. This "fake" type just displays
 * some HTML tags and it must be added to a form as "unmapped" and "non required".
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @deprecated since 4.8.0, use the alternatives types in the 'EasyCorp\Bundle\EasyAdminBundle\Form\Type\Layout' namespace instead
 */
class EaFormFieldsetType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'ea_form_fieldset';
    }
}
