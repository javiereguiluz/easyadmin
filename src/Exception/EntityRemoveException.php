<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Exception;

use EasyCorp\Bundle\EasyAdminBundle\Context\ExceptionContext;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class EntityRemoveException extends BaseException
{
    public function __construct(array $parameters = [], ?\Throwable $previous = null)
    {
        $exceptionContext = new ExceptionContext(
            'exception.entity_remove',
            sprintf('There is a ForeignKeyConstraintViolationException for the Doctrine entity associated with "%s". Solution: disable the "delete" action for this CRUD controller or configure the "cascade={"remove"}" attribute for the related field in the Doctrine entity. Full exception: %s', $parameters['entity_name'], $parameters['message']),
            $parameters,
            409
        );

        parent::__construct($exceptionContext, $previous);
    }
}
