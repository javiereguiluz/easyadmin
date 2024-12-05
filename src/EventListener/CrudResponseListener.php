<?php

namespace EasyCorp\Bundle\EasyAdminBundle\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CrudResponseListener
{
    public function __construct(
        private readonly AdminContextProviderInterface $adminContextProvider,
        private Environment $twig,
    ) {
    }

    public function onKernelView(ViewEvent $event): void
    {
        $responseParameters = $event->getControllerResult();
        if (!$responseParameters instanceof KeyValueStore) {
            return;
        }

        if (!$responseParameters->has('templateName') && !$responseParameters->has('templatePath')) {
            throw new \RuntimeException('The KeyValueStore object returned by CrudController actions must include either a "templateName" or a "templatePath" parameter to define the template used to render the action result.');
        }

        $templateParameters = $responseParameters->all();
        $templatePath = \array_key_exists('templatePath', $templateParameters)
            ? $templateParameters['templatePath']
            : $this->adminContextProvider->getContext()->getTemplatePath($templateParameters['templateName']);

        // to make parameters easier to modify, we pass around FormInterface objects
        // so we must convert those values to FormView before rendering the template
        $formErrorCount = 0;
        foreach ($templateParameters as $paramName => $paramValue) {
            if ($paramValue instanceof FormInterface) {
                $templateParameters[$paramName] = $paramValue->createView();
                $formErrorCount = max($formErrorCount, \count($paramValue->getErrors(true)));
            }
        }
        $httpCode = $formErrorCount > 0 ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK;
        $event->setResponse(new Response($this->twig->render($templatePath, $templateParameters), $httpCode));
    }
}
