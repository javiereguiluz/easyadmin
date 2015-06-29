<?php

/*
 * This file is part of the EasyAdminBundle.
 *
 * (c) Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JavierEguiluz\Bundle\EasyAdminBundle\Exception;

class ForbiddenActionException extends BaseException
{
    public function __construct(array $parameters = array())
    {
        $parameters['message'] = sprintf('The requested <code>%s</code> action is not allowed.', $parameters['action']);

        parent::__construct($parameters);
    }
}
