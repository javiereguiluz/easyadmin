<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Extension that injects EasyAdmin related information in the view used to
 * render the form.
 *
 * @author Maxime Steinhausser <maxime.steinhausser@gmail.com>
 */
class EasyAdminExtension extends AbstractTypeExtension
{
    /** @var Request|null */
    private $request;

    /** @var Request|null */
    private $requestStack;

    /**
     * EasyAdminExtension constructor.
     *
     * @param RequestStack|null $requestStack
     */
    public function __construct(RequestStack $requestStack = null)
    {
        $this->requestStack = $requestStack;
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($this->requestStack !== null) {
            $this->request = $this->requestStack->getCurrentRequest();
        }

        if (null === $this->request) {
            return;
        }

        if ($this->request->attributes->has('easyadmin')) {
            $easyadmin = $this->request->attributes->get('easyadmin');
            $entity = $easyadmin['entity'];
            $action = $easyadmin['view'];
            $fields = $entity[$action]['fields'];
            $view->vars['easyadmin'] = array(
                'entity' => $entity,
                'view' => $action,
                'item' => $easyadmin['item'],
                'field' => isset($fields[$view->vars['name']]) ? $fields[$view->vars['name']] : null,
            );
        }
    }

    /**
     * BC for SF < 2.4.
     * To be replaced by the usage of the request stack when 2.3 support is dropped.
     *
     * @param Request|null $request
     *
     * @return $this
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return class_exists('Symfony\Component\Form\Util\StringUtil')
            ? 'Symfony\Component\Form\Extension\Core\Type\FormType' : 'form';
    }
}
