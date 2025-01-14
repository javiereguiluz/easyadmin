<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
class BatchActionDto
{
    private string $name;
    private array $entityIds;
    private string $entityFqcn;
    private string $referrerUrl;
    private string $csrfToken;

    public function __construct(string $name, array $entityIds, string $entityFqcn, string $referrerUrl, string $csrfToken /* , bool $triggerDeprecation = true */)
    {
        $this->name = $name;
        $this->entityIds = $entityIds;
        $this->entityFqcn = $entityFqcn;

        // the $referrerUrl argument is deprecated; instead of removing it, do this:
        //   * if the user passes 5 arguments to the constructor, trigger a deprecation message
        //     and assign the 4th argument to referrerUrl and the fifth to csrfToken;
        //   * if the user passes 4 arguments, skip the referrer and assign the 4th to csrfToken
        if (\func_num_args() > 4) {
            // the sixth unofficial argument allows to skip deprecations when using this method by our own code
            if (5 === \func_num_args() || (6 === \func_num_args() && false !== func_get_arg(5))) {
                trigger_deprecation(
                    'easycorp/easyadmin-bundle',
                    '4.22.0',
                    'Passing the referrer URL to the "%s" constructor is deprecated. The referrer URL will now be determined automatically based on the current request.',
                    self::class
                );
            }

            $this->referrerUrl = $referrerUrl;
            $this->csrfToken = $csrfToken;
        } else {
            $this->csrfToken = $referrerUrl;
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEntityIds(): array
    {
        return $this->entityIds;
    }

    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }

    public function getReferrerUrl(): string
    {
        trigger_deprecation(
            'easycorp/easyadmin-bundle',
            '4.22.0',
            'The referrer of batch action DTOs is deprecated and it will be removed in 5.0.0. Use $adminContext->getRequest()->headers->get(\'referer\') or redirect to some specific URL.',
        );

        return $this->referrerUrl;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}
