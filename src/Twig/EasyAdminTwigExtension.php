<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Twig;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Defines the filters and functions used to render the bundle's templates.
 * Also injects the admin context into Twig global variables as `ea` in order
 * to be used by admin templates.
 *
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Benjamin Georgeault <git@wedgesama.fr>
 */
class EasyAdminTwigExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly AdminContextProviderInterface $adminContextProvider,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ea_url', [$this, 'getAdminUrlGenerator']),
            new TwigFunction('ea_form_ealabel', null, ['node_class' => 'Symfony\Bridge\Twig\Node\SearchAndRenderBlockNode', 'is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ea_flatten_array', [$this, 'flattenArray']),
            new TwigFilter('ea_filesize', [$this, 'fileSize']),
            new TwigFilter('ea_as_string', [$this, 'representAsString']),
        ];
    }

    public function getGlobals(): array
    {
        // this is needed to make the admin context available on any Twig template via the short named variable 'ea'
        return ['ea' => $this->adminContextProvider];
    }

    /**
     * Transforms ['a' => 'foo', 'b' => ['c' => ['d' => 7]]] into ['a' => 'foo', 'b[c][d]' => 7]
     * It's useful to submit nested arrays (e.g. query string parameters) as form fields.
     */
    public function flattenArray($array, $parentKey = null): array
    {
        $flattenedArray = [];

        foreach ($array as $flattenedKey => $value) {
            $flattenedKey = null !== $parentKey ? sprintf('%s[%s]', $parentKey, $flattenedKey) : $flattenedKey;

            if (\is_array($value)) {
                $flattenedArray = array_merge($flattenedArray, $this->flattenArray($value, $flattenedKey));
            } else {
                $flattenedArray[$flattenedKey] = $value;
            }
        }

        return $flattenedArray;
    }

    public function fileSize(int $bytes): string
    {
        $size = ['B', 'K', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'];
        $factor = (int) floor(log($bytes) / log(1024));

        return (int) ($bytes / (1024 ** $factor)).@$size[$factor];
    }

    public function representAsString($value, string|callable|null $toStringMethod = null): string
    {
        if (null !== $toStringMethod) {
            if (\is_callable($toStringMethod)) {
                return $toStringMethod($value, $this->translator);
            }

            $callable = [$value, $toStringMethod];
            if (!\is_callable($callable) || !method_exists($value, $toStringMethod)) {
                throw new \RuntimeException(sprintf('The method "%s()" does not exist or is not callable in the value of type "%s"', $toStringMethod, \is_object($value) ? $value::class : \gettype($value)));
            }

            return \call_user_func($callable);
        }

        if (null === $value) {
            return '';
        }

        if (\is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (\is_array($value)) {
            return sprintf('Array (%d items)', \count($value));
        }

        if (\is_object($value)) {
            if ($value instanceof TranslatableInterface) {
                return $value->trans($this->translator);
            }

            if (method_exists($value, '__toString')) {
                return (string) $value;
            }

            if (method_exists($value, 'getId')) {
                return sprintf(
                    '%s #%s',
                    // remove null bytes from class name (this happens in anonymous classes)
                    str_replace("\0", '', $value::class),
                    $value->getId()
                );
            }

            return sprintf(
                '%s #%s',
                // remove null bytes from class name (this happens in anonymous classes)
                str_replace("\0", '', $value::class),
                hash('xxh32', (string) spl_object_id($value))
            );
        }

        return '';
    }

    public function getAdminUrlGenerator(array $queryParameters = []): AdminUrlGeneratorInterface
    {
        return $this->serviceLocator->get(AdminUrlGenerator::class)->setAll($queryParameters);
    }
}
