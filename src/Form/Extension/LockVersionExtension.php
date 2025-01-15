<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Form\Extension;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\LockableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Form\EventListener\LockVersionValidationListener;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Form extension to handle entity lock versioning.
 *
 * @author Ahmed EBEN HASSINE <ahmedbhs123@gmail.com>
 */
class LockVersionExtension extends AbstractTypeExtension
{
    private LockVersionValidationListener $validationListener;
    private AdminContextProvider $adminContextProvider;

    public function __construct(LockVersionValidationListener $validationListener, AdminContextProvider $adminContextProvider)
    {
        $this->validationListener = $validationListener;
        $this->adminContextProvider = $adminContextProvider;
    }

    /**
     * Builds form by adding lock version field for root entities.
     *
     * @param FormBuilderInterface $builder Form builder instance
     * @param array                $options Form build options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $eaContext = $this->adminContextProvider->getContext();

        if (null === $eaContext) {
            return;
        }

        if (Crud::PAGE_EDIT !== $eaContext->getCrud()->getCurrentAction()) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            $form = $event->getForm();

            // Add lock version field for root entities with getLockVersion method
            if ($form->isRoot() && null !== $entity && $entity instanceof LockableInterface) {
                $form->add(EA::LOCK_VERSION, HiddenType::class, [
                    'mapped' => false,
                    'data' => $entity->getLockVersion(),
                ]);
            }
        });

        $builder->addEventSubscriber($this->validationListener);
    }

    /**
     * Specifies the extended form types.
     *
     * @return iterable<string> List of extended form types
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}
