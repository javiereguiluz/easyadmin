<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\SearchMode;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Translation\TranslatableMessageBuilder;
use Symfony\Component\ExpressionLanguage\Expression;
use function Symfony\Component\Translation\t;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 */
final class CrudDto
{
    private ?string $controllerFqcn = null;
    private AssetsDto $fieldAssetsDto;
    private ?string $pageName = null;
    private ?string $actionName = null;
    private ?ActionConfigDto $actionConfigDto = null;
    private ?FilterConfigDto $filters = null;
    private ?string $entityFqcn = null;
    private $entityLabelInSingular;
    private $entityLabelInPlural;
    private array $defaultPageTitles = [
        Crud::PAGE_DETAIL => 'page_title.detail',
        Crud::PAGE_EDIT => 'page_title.edit',
        Crud::PAGE_INDEX => 'page_title.index',
        Crud::PAGE_NEW => 'page_title.new',
    ];
    private array $customPageTitles = [
        Crud::PAGE_DETAIL => null,
        Crud::PAGE_EDIT => null,
        Crud::PAGE_INDEX => null,
        Crud::PAGE_NEW => null,
    ];
    private array $helpMessages = [
        Crud::PAGE_DETAIL => null,
        Crud::PAGE_EDIT => null,
        Crud::PAGE_INDEX => null,
        Crud::PAGE_NEW => null,
    ];
    private ?string $datePattern = 'medium';
    private ?string $timePattern = 'medium';
    private array $dateTimePattern = ['medium', 'medium'];
    private string $dateIntervalFormat = '%%y Year(s) %%m Month(s) %%d Day(s)';
    private ?string $timezone = null;
    private ?string $numberFormat = null;
    private ?string $thousandsSeparator = null;
    private ?string $decimalSeparator = null;
    private array $defaultSort = [];
    private ?array $searchFields = [];
    private string $searchMode = SearchMode::ALL_TERMS;
    private bool $autofocusSearch = false;
    private bool $showEntityActionsAsDropdown = true;
    private ?PaginatorDto $paginatorDto = null;
    private $overriddenTemplates;
    private array $formThemes = ['@EasyAdmin/crud/form_theme.html.twig'];
    private KeyValueStore $newFormOptions;
    private KeyValueStore $editFormOptions;
    private string|Expression|null $entityPermission = null;
    private ?string $contentWidth = null;
    private ?string $sidebarWidth = null;
    private bool $hideNullValues = false;
    private array $htmlAttributes = [];

    public function __construct()
    {
        $this->fieldAssetsDto = new AssetsDto();
        $this->newFormOptions = KeyValueStore::new();
        $this->editFormOptions = KeyValueStore::new();
        $this->overriddenTemplates = [];
    }

    public function getControllerFqcn(): ?string
    {
        return $this->controllerFqcn;
    }

    public function setControllerFqcn(string $fqcn): void
    {
        $this->controllerFqcn = $fqcn;
    }

    public function getCurrentPage(): ?string
    {
        return $this->pageName;
    }

    public function setPageName(?string $pageName): void
    {
        $this->pageName = $pageName;
    }

    public function getFieldAssets(string $pageName): AssetsDto
    {
        return $this->fieldAssetsDto;
    }

    public function setFieldAssets(AssetsDto $assets): void
    {
        $this->fieldAssetsDto = $assets;
    }

    public function getEntityFqcn(): string
    {
        return $this->entityFqcn;
    }

    public function setEntityFqcn(string $entityFqcn): void
    {
        $this->entityFqcn = $entityFqcn;
    }

    public function getEntityLabelInSingular($entityInstance = null, $pageName = null): TranslatableInterface|string|null
    {
        if (null === $this->entityLabelInSingular) {
            return null;
        }

        if (
            \is_string($this->entityLabelInSingular)
            || $this->entityLabelInSingular instanceof TranslatableInterface
        ) {
            return $this->entityLabelInSingular;
        }

        return ($this->entityLabelInSingular)($entityInstance, $pageName);
    }

    /**
     * @param TranslatableInterface|string|callable $label
     */
    public function setEntityLabelInSingular($label): void
    {
        $this->entityLabelInSingular = $label;
    }

    public function getEntityLabelInPlural($entityInstance = null, $pageName = null): TranslatableInterface|string|null
    {
        if (null === $this->entityLabelInPlural) {
            return null;
        }

        if (
            \is_string($this->entityLabelInPlural)
            || $this->entityLabelInPlural instanceof TranslatableInterface
        ) {
            return $this->entityLabelInPlural;
        }

        return ($this->entityLabelInPlural)($entityInstance, $pageName);
    }

    /**
     * @param TranslatableInterface|string|callable $label
     */
    public function setEntityLabelInPlural($label): void
    {
        $this->entityLabelInPlural = $label;
    }

    public function getCustomPageTitle(?string $pageName = null, $entityInstance = null, array $translationParameters = [], ?string $domain = null): ?TranslatableInterface
    {
        $title = $this->customPageTitles[$pageName ?? $this->pageName];
        if (\is_callable($title)) {
            $title = null !== $entityInstance ? $title($entityInstance) : $title();
        }

        if (null === $title) {
            return null;
        }

        if ($title instanceof TranslatableInterface) {
            return TranslatableMessageBuilder::withParameters($title, $translationParameters);
        }

        return t($title, $translationParameters, $domain);
    }

    /**
     * @param TranslatableInterface|string|callable $pageTitle
     */
    public function setCustomPageTitle(string $pageName, $pageTitle): void
    {
        if (!\is_string($pageTitle) && !$pageTitle instanceof TranslatableInterface && !\is_callable($pageTitle)) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$pageTitle',
                __METHOD__,
                '"string" or "callable"',
                \gettype($pageTitle)
            );
        }

        $this->customPageTitles[$pageName] = $pageTitle;
    }

    public function getDefaultPageTitle(?string $pageName = null, /* ?object */ $entityInstance = null, array $translationParameters = []): ?TranslatableInterface
    {
        if (!\is_object($entityInstance)
            && null !== $entityInstance) {
            trigger_deprecation(
                'easycorp/easyadmin-bundle',
                '4.0.5',
                'Argument "%s" for "%s" must be one of these types: %s. Passing type "%s" will cause an error in 5.0.0.',
                '$entityInstance',
                __METHOD__,
                '"object" or "null"',
                \gettype($entityInstance)
            );
        }

        if (null !== $entityInstance) {
            if (method_exists($entityInstance, '__toString')) {
                $entityAsString = (string) $entityInstance;

                if ('' !== $entityAsString) {
                    return t($entityAsString, $translationParameters, 'EasyAdminBundle');
                }
            }
        }

        if (!$this->defaultPageTitles[$pageName ?? $this->pageName]) {
            return null;
        }

        return t($this->defaultPageTitles[$pageName ?? $this->pageName], $translationParameters, 'EasyAdminBundle');
    }

    public function getHelpMessage(?string $pageName = null): TranslatableInterface|string
    {
        return $this->helpMessages[$pageName ?? $this->pageName] ?? '';
    }

    public function getHelpMessages(): array
    {
        return $this->helpMessages;
    }

    public function setHelpMessage(string $pageName, TranslatableInterface|string $helpMessage): void
    {
        $this->helpMessages[$pageName] = $helpMessage;
    }

    public function getDatePattern(): ?string
    {
        return $this->datePattern;
    }

    public function setDatePattern(?string $format): void
    {
        $this->datePattern = $format;
    }

    public function getTimePattern(): ?string
    {
        return $this->timePattern;
    }

    public function setTimePattern(?string $format): void
    {
        $this->timePattern = $format;
    }

    public function getDateTimePattern(): array
    {
        return $this->dateTimePattern;
    }

    public function setDateTimePattern(string $dateFormatOrPattern, string $timeFormat = DateTimeField::FORMAT_NONE): void
    {
        $this->dateTimePattern = [$dateFormatOrPattern, $timeFormat];
    }

    public function getDateIntervalFormat(): string
    {
        return $this->dateIntervalFormat;
    }

    public function setDateIntervalFormat(string $format): void
    {
        $this->dateIntervalFormat = $format;
    }

    public function getTimezone(): ?string
    {
        return $this->timezone;
    }

    public function setTimezone(string $timezoneId): void
    {
        $this->timezone = $timezoneId;
    }

    public function getNumberFormat(): ?string
    {
        return $this->numberFormat;
    }

    public function setNumberFormat(string $numberFormat): void
    {
        $this->numberFormat = $numberFormat;
    }

    public function getThousandsSeparator(): ?string
    {
        return $this->thousandsSeparator;
    }

    public function setThousandsSeparator(string $separator): void
    {
        $this->thousandsSeparator = $separator;
    }

    public function getDecimalSeparator(): ?string
    {
        return $this->decimalSeparator;
    }

    public function setDecimalSeparator(string $separator): void
    {
        $this->decimalSeparator = $separator;
    }

    public function getDefaultSort(): array
    {
        return $this->defaultSort;
    }

    public function setDefaultSort(array $defaultSort): void
    {
        $this->defaultSort = $defaultSort;
    }

    public function getSearchMode(): string
    {
        return $this->searchMode;
    }

    public function setSearchMode(string $searchMode): void
    {
        $this->searchMode = $searchMode;
    }

    public function getSearchFields(): ?array
    {
        return $this->searchFields;
    }

    public function setSearchFields(?array $searchFields): void
    {
        $this->searchFields = $searchFields;
    }

    public function autofocusSearch(): bool
    {
        return $this->autofocusSearch;
    }

    public function setAutofocusSearch(bool $autofocusSearch): void
    {
        $this->autofocusSearch = $autofocusSearch;
    }

    public function isSearchEnabled(): bool
    {
        return null !== $this->searchFields;
    }

    public function showEntityActionsAsDropdown(): bool
    {
        return $this->showEntityActionsAsDropdown;
    }

    public function setShowEntityActionsAsDropdown(bool $showAsDropdown): void
    {
        $this->showEntityActionsAsDropdown = $showAsDropdown;
    }

    public function getPaginator(): PaginatorDto
    {
        return $this->paginatorDto;
    }

    public function setPaginator(PaginatorDto $paginatorDto): void
    {
        $this->paginatorDto = $paginatorDto;
    }

    public function getOverriddenTemplates(): array
    {
        return $this->overriddenTemplates;
    }

    public function overrideTemplate(string $templateName, string $templatePath): void
    {
        $this->overriddenTemplates[$templateName] = $templatePath;
    }

    public function getFormThemes(): array
    {
        return $this->formThemes;
    }

    public function addFormTheme(string $formThemePath): void
    {
        // fields form themes are added last to give them more priority
        $this->formThemes = array_merge($this->formThemes, [$formThemePath]);
    }

    public function setFormThemes(array $formThemes): void
    {
        $this->formThemes = $formThemes;
    }

    public function getNewFormOptions(): KeyValueStore
    {
        return $this->newFormOptions;
    }

    public function getEditFormOptions(): KeyValueStore
    {
        return $this->editFormOptions;
    }

    public function setNewFormOptions(KeyValueStore $formOptions): void
    {
        $this->newFormOptions = $formOptions;
    }

    public function setEditFormOptions(KeyValueStore $formOptions): void
    {
        $this->editFormOptions = $formOptions;
    }

    public function getEntityPermission(): string|Expression|null
    {
        return $this->entityPermission;
    }

    public function setEntityPermission(string|Expression $entityPermission): void
    {
        $this->entityPermission = $entityPermission;
    }

    public function getCurrentAction(): string
    {
        return $this->actionName;
    }

    public function setCurrentAction(string $actionName): void
    {
        $this->actionName = $actionName;
    }

    public function getActionsConfig(): ActionConfigDto
    {
        return $this->actionConfigDto;
    }

    public function setActionsConfig(ActionConfigDto $actionConfig): void
    {
        $this->actionConfigDto = $actionConfig;
    }

    public function getFiltersConfig(): FilterConfigDto
    {
        return $this->filters;
    }

    public function setFiltersConfig(FilterConfigDto $filterConfig): void
    {
        $this->filters = $filterConfig;
    }

    public function getContentWidth(): ?string
    {
        return $this->contentWidth;
    }

    public function setContentWidth(string $contentWidth): void
    {
        $this->contentWidth = $contentWidth;
    }

    public function getSidebarWidth(): ?string
    {
        return $this->sidebarWidth;
    }

    public function setSidebarWidth(string $sidebarWidth): void
    {
        $this->sidebarWidth = $sidebarWidth;
    }

    public function areNullValuesHidden(): bool
    {
        return $this->hideNullValues;
    }

    public function hideNullValues(bool $hide): void
    {
        $this->hideNullValues = $hide;
    }

    public function getHtmlAttributes(): array
    {
        return $this->htmlAttributes;
    }

    public function setHtmlAttributes(array $htmlAttributes): void
    {
        $this->htmlAttributes = $htmlAttributes;
    }

    public function setHtmlAttribute(string $attribute, mixed $value): void
    {
        $this->htmlAttributes[$attribute] = $value;
    }
}
