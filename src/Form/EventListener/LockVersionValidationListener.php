<?php

declare(strict_types=1);

namespace EasyCorp\Bundle\EasyAdminBundle\Form\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\LockableInterface;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates lock version to prevent concurrent entity modifications.
 *
 * @author Ahmed EBEN HASSINE <ahmedbhs123@gmail.com>
 */
class LockVersionValidationListener implements EventSubscriberInterface
{
    private AdminUrlGeneratorInterface $adminUrlGenerator;
    private TranslatorInterface $translator;
    private AdminContextProvider $adminContextProvider;

    public function __construct(
        AdminUrlGeneratorInterface $adminUrlGenerator,
        TranslatorInterface $translator,
        AdminContextProvider $adminContextProvider,
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->translator = $translator;
        $this->adminContextProvider = $adminContextProvider;
    }

    /**
     * Returns the events this listener subscribes to.
     *
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPostSubmit',
        ];
    }

    /**
     * Validates the lock version before form submission.
     *
     * @throws \RuntimeException If lock version cannot be determined
     */
    public function onPostSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        $instance = $form->getData();

        // Only proceed for root forms with lock-capable entities
        if (!($form->isRoot() && $instance instanceof LockableInterface)) {
            return;
        }

        // Extract submitted lock version
        $submittedLockVersion = $data[EA::LOCK_VERSION] ?? null;
        if (null === $submittedLockVersion) {
            return;
        }

        $eaContext = $this->adminContextProvider->getContext();
        if (!$eaContext instanceof AdminContext) {
            return;
        }

        $currentLockVersion = $instance->getLockVersion();
        if (null === $currentLockVersion) {
            throw new \RuntimeException('Lock version not found in the database.');
        }

        // Check for version mismatch and add error if needed
        if ((int) $submittedLockVersion !== $currentLockVersion) {
            $targetUrl = $this->adminUrlGenerator
                ->setController($eaContext->getCrud()->getControllerFqcn())
                ->setDashboard($eaContext->getDashboardControllerFqcn())
                ->setAction(Crud::PAGE_EDIT)
                ->generateUrl();

            $message = $this->translator->trans('flash_lock_error.message', [
                '%link_start%' => sprintf('<a href="%s" class="info-link">', $targetUrl),
                '%link_end%' => '</a>',
                '%reload_text%' => $this->translator->trans('flash_lock_error.reload_page', [], 'EasyAdminBundle'),
            ], 'EasyAdminBundle');

            $form->addError(new FormError($message));
        }
    }
}
