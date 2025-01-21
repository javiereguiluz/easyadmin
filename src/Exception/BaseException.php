<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Exception;

use EasyCorp\Bundle\EasyAdminBundle\Context\ExceptionContext;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class BaseException extends HttpException
{
    private ExceptionContext $context;

    public function __construct(ExceptionContext $context, ?\Throwable $previous = null)
    {
        $this->context = $context;
        parent::__construct($this->context->getStatusCode(), $this->context->getDebugMessage(), $previous);
    }

    public function getContext(): ExceptionContext
    {
        return $this->context;
    }

    /**
     * @return string The message that can safely be displayed to end-users because it doesn't contain sensitive data
     */
    public function getPublicMessage(): string
    {
        return $this->context->getPublicMessage();
    }

    /**
     * @return string The full exception message that is logged and it can contain sensitive data
     */
    public function getDebugMessage(): string
    {
        return $this->context->getDebugMessage();
    }

    public function getParameters(): array
    {
        return $this->context->getParameters();
    }

    public function getStatusCode(): int
    {
        return $this->context->getStatusCode();
    }
}
