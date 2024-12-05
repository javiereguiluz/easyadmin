// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');

import bootstrap from 'bootstrap/dist/js/bootstrap.bundle';
import Mark from 'mark.js/src/vanilla';
import Autocomplete from './autocomplete';
import {toggleVisibilityClasses} from "./helpers";

// Provide Bootstrap variable globally to allow custom backend pages to use it
window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    window.EasyAdminApp = new App();
});

class App {
    #sidebarWidthLocalStorageKey;
    #contentWidthLocalStorageKey;

    constructor() {
        this.#sidebarWidthLocalStorageKey = 'ea/sidebar/width';
        this.#contentWidthLocalStorageKey = 'ea/content/width';

        this.#removeHashFormUrl();
        this.#createMainMenu();
        this.#createLayoutResizeControls();
        this.#createNavigationToggler();
        this.#createSearchHighlight();
        this.#createFilters();
        this.#createAutoCompleteFields();
        this.#createBatchActions();
        this.#createModalWindowsForDeleteActions();
        this.#createPopovers();
        this.#createTooltips();

        document.addEventListener('ea.collection.item-added', () => this.#createAutoCompleteFields());
    }

    // When using tabs in forms, the selected tab is persisted (in the URL hash) so you
    // can see the same tab when reloading the page (e.g. '#tab-contact-information').
    // This method removes the hash from URL in the index page to not show form-related
    // information in the index page
    #removeHashFormUrl() {
        if (!window.location.href.includes('#')) {
            return;
        }

        // remove the hash only in the index page
        if (!document.querySelector('body').classList.contains('ea-index')) {
            return;
        }

        // don't set the hash to '' because that also removes the query parameters
        const urlParts = window.location.href.split('#');
        const urlWithoutHash = urlParts[0];
        window.history.replaceState({}, '', urlWithoutHash);
    }

    #createMainMenu() {
        // inspired by https://codepen.io/phileflanagan/pen/mwpQpY
        const menuItemsWithSubmenus = document.querySelectorAll('#main-menu .menu-item.has-submenu');
        menuItemsWithSubmenus.forEach((menuItem) => {
            const menuItemSubmenu = menuItem.querySelector('.submenu');
            if (null === menuItemSubmenu) {
                return;
            }

            // needed because the menu accordion is based on the max-block-size property.
            // visible elements must be initialized with a explicit max-block-size; otherwise
            // when you click on them the first time, the animation is not smooth
            if (menuItem.classList.contains('expanded')) {
                menuItemSubmenu.style.maxHeight = menuItemSubmenu.scrollHeight + 'px';
            }

            menuItem.querySelector('.submenu-toggle').addEventListener('click', (event) =>  {
                event.preventDefault();

                // hide other submenus
                menuItemsWithSubmenus.forEach((otherMenuItem) => {
                    if (menuItem === otherMenuItem) {
                        return;
                    }

                    const otherMenuItemSubmenu = otherMenuItem.querySelector('.submenu');
                    if (otherMenuItem.classList.contains('expanded')) {
                        otherMenuItemSubmenu.style.maxHeight = '0px';
                        otherMenuItem.classList.remove('expanded');
                    }
                });

                // toggle the state of this submenu
                if (menuItem.classList.contains('expanded')) {
                    menuItemSubmenu.style.maxHeight = '0px';
                    menuItem.classList.remove('expanded');
                } else {
                    menuItemSubmenu.style.maxHeight = menuItemSubmenu.scrollHeight + 'px';
                    menuItem.classList.add('expanded');
                }
            });
        });
    }

    #createLayoutResizeControls() {
        const sidebarResizerHandler = document.querySelector('#sidebar-resizer-handler');
        if (null !== sidebarResizerHandler) {
            sidebarResizerHandler.addEventListener('click', () => {
                const oldValue = localStorage.getItem(this.#sidebarWidthLocalStorageKey) || 'normal';
                const newValue = 'normal' === oldValue ? 'compact' : 'normal';

                document.querySelector('body').classList.remove(`ea-sidebar-width-${ oldValue }`);
                document.querySelector('body').classList.add(`ea-sidebar-width-${ newValue }`);
                localStorage.setItem(this.#sidebarWidthLocalStorageKey, newValue);
            });
        }

        const contentResizerHandler = document.querySelector('#content-resizer-handler');
        if (null !== contentResizerHandler) {
            contentResizerHandler.addEventListener('click', () => {
                const oldValue = localStorage.getItem(this.#contentWidthLocalStorageKey) || 'normal';
                const newValue = 'normal' === oldValue ? 'full' : 'normal';

                document.querySelector('body').classList.remove(`ea-content-width-${ oldValue }`);
                document.querySelector('body').classList.add(`ea-content-width-${ newValue }`);
                localStorage.setItem(this.#contentWidthLocalStorageKey, newValue);
            });
        }
    }

    #createNavigationToggler() {
        const toggler = document.querySelector('#navigation-toggler');
        const cssClassName = 'ea-mobile-sidebar-visible';
        let modalBackdrop;

        if (null === toggler) {
            return;
        }

        toggler.addEventListener('click', () => {
            document.querySelector('body').classList.toggle(cssClassName);

            if (document.querySelector('body').classList.contains(cssClassName)) {
                modalBackdrop = document.createElement('div');
                modalBackdrop.classList.add('modal-backdrop', 'fade', 'show');
                modalBackdrop.onclick = () => {
                    document.querySelector('body').classList.remove(cssClassName);
                    document.body.removeChild(modalBackdrop);
                    modalBackdrop = null;
                };

                document.body.appendChild(modalBackdrop);
            } else if (modalBackdrop) {
                document.body.removeChild(modalBackdrop);
                modalBackdrop = null;
            }
        });
    }

    #createSearchHighlight() {
        const searchElement = document.querySelector('.form-action-search [name="query"]');
        if (null === searchElement) {
            return;
        }

        const searchQuery = searchElement.value;
        if ('' === searchQuery.trim()) {
            return;
        }

        // splits a string into tokens, taking into account quoted strings
        // Example: 'foo "bar baz" qux' => ['foo', 'bar baz', 'qux']
        const tokenizeString = (string) => {
            const regex = /"([^"\\]*(\\.[^"\\]*)*)"|\S+/g;
            const tokens = [];
            let match;

            while (null !== (match = regex.exec(string))) {
                tokens.push(match[0].replaceAll('"', '').trim());
            }

            return tokens;
        };

        const searchQueryTerms = tokenizeString(searchElement.value);
        const searchQueryTermsHighlightRegexp = new RegExp(
            searchQueryTerms
                // escapes all characters that are special inside a RegExp
                .map(term => term.replace(/[|\\{}()[\]^$+*?.-]/g, '\\$&'))
                .join('|'),
            'i'
        );

        const elementsToHighlight = document.querySelectorAll('table tbody td.searchable');
        const highlighter = new Mark(elementsToHighlight);
        highlighter.markRegExp(searchQueryTermsHighlightRegexp);
    }

    #createFilters() {
        const filterButton = document.querySelector('.datagrid-filters .action-filters-button');
        if (null === filterButton) {
            return;
        }

        const filterModal = document.querySelector(filterButton.getAttribute('data-bs-target'));

        // this is needed to avoid errors when connection is slow
        filterButton.setAttribute('href', filterButton.getAttribute('data-href'));
        filterButton.removeAttribute('data-href');
        filterButton.classList.remove('disabled');

        filterButton.addEventListener('click', (event) => {
            const filterModalBody = filterModal.querySelector('.modal-body');
            filterModalBody.innerHTML = '<div class="fa-3x px-3 py-3 text-muted text-center"><i class="fas fa-circle-notch fa-spin"></i></div>';

            fetch(filterButton.getAttribute('href'))
                .then((response) => { return response.text(); })
                .then((text) => {
                    filterModalBody.innerHTML = text;
                    this.#createAutoCompleteFields();
                    this.#createFilterToggles();
                })
                .catch((error) => { console.error(error); });

            event.preventDefault();
        });

        const removeFilter = (filterField) => {
            filterField.closest('form').querySelectorAll(`input[name^="filters[${filterField.dataset.filterProperty}]"]`).forEach((filterFieldInput) => {
                filterFieldInput.remove();
            });

            filterField.remove();
        };

        document.querySelector('#modal-clear-button').addEventListener('click', () => {
            filterModal.querySelectorAll('.filter-field').forEach((filterField) => {
                removeFilter(filterField);
            });
            filterModal.querySelector('form').submit();
        });

        document.querySelector('#modal-apply-button').addEventListener('click', () => {
            filterModal.querySelectorAll('.filter-checkbox:not(:checked)').forEach((notAppliedFilter) => {
                removeFilter(notAppliedFilter.closest('.filter-field'));
            });
            filterModal.querySelector('form').submit();
        });
    }

    #createBatchActions() {
        let lastUpdatedRowCheckbox = null;
        const selectAllCheckbox = document.querySelector('.form-batch-checkbox-all');
        if (null === selectAllCheckbox) {
            return;
        }

        const rowCheckboxes = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox');
        selectAllCheckbox.addEventListener('change', () => {
            rowCheckboxes.forEach((rowCheckbox) => {
                rowCheckbox.checked = selectAllCheckbox.checked;
                rowCheckbox.dispatchEvent(new Event('change'));
            });
        });

        const deselectAllButton = document.querySelector('.deselect-batch-button');
        if (null !== deselectAllButton) {
            deselectAllButton.addEventListener('click', () => {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.dispatchEvent(new Event('change'));
            });
        }

        rowCheckboxes.forEach((rowCheckbox, rowCheckboxIndex) => {
            rowCheckbox.dataset.rowIndex = rowCheckboxIndex;

            rowCheckbox.addEventListener('click', (e) => {
                if (lastUpdatedRowCheckbox && e.shiftKey) {
                    const lastIndex = parseInt(lastUpdatedRowCheckbox.dataset.rowIndex);
                    const currentIndex = parseInt(e.target.dataset.rowIndex);
                    const valueToApply = e.target.checked;
                    const lowest = Math.min(lastIndex, currentIndex);
                    const highest = Math.max(lastIndex, currentIndex);

                    rowCheckboxes.forEach((rowCheckbox2, rowCheckboxIndex2) => {
                        if (lowest <= rowCheckboxIndex2 && rowCheckboxIndex2 <= highest) {
                            rowCheckbox2.checked = valueToApply;
                            rowCheckbox2.dispatchEvent(new Event('change'));
                        }
                    });
                }
                lastUpdatedRowCheckbox = e.target;
            });

            rowCheckbox.addEventListener('change', () => {
                const selectedRowCheckboxes = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox:checked');
                const row = rowCheckbox.closest('tr');
                const content = rowCheckbox.closest('.content');

                if (rowCheckbox.checked) {
                    row.classList.add('selected-row');
                } else {
                    row.classList.remove('selected-row');
                    selectAllCheckbox.checked = false;
                }

                const rowsAreSelected = 0 !== selectedRowCheckboxes.length;
                const contentTitle = document.querySelector('.content-header-title > .title');
                const filters = content.querySelector('.datagrid-filters');
                const globalActions = content.querySelector('.global-actions');
                const batchActions = content.querySelector('.batch-actions');

                if (null !== contentTitle) {
                    toggleVisibilityClasses(contentTitle, rowsAreSelected);
                }
                if (null !== filters) {
                    toggleVisibilityClasses(filters, rowsAreSelected);
                }
                if (null !== globalActions) {
                    toggleVisibilityClasses(globalActions, rowsAreSelected);
                }
                if (null !== batchActions) {
                    toggleVisibilityClasses(batchActions, !rowsAreSelected);
                }
            });
        });

        const modalTitle = document.querySelector('#batch-action-confirmation-title');
        const titleContentWithPlaceholders = modalTitle.textContent;

        document.querySelectorAll('[data-action-batch]').forEach((dataActionBatch) => {
            dataActionBatch.addEventListener('click', (event) => {
                event.preventDefault();

                const actionElement = event.currentTarget;
                // There is still a possibility that actionName will remain undefined. The title attribute is not always present on elements with the [data-action-batch] attribute.
                const actionName = actionElement.textContent.trim() || actionElement.getAttribute('title');
                const selectedItems = document.querySelectorAll('input[type="checkbox"].form-batch-checkbox:checked');
                modalTitle.textContent = titleContentWithPlaceholders
                    .replace('%action_name%', actionName)
                    .replace('%num_items%', selectedItems.length.toString());

                document.querySelector('#modal-batch-action-button').addEventListener('click', () => {
                    // prevent double submission of the batch action form
                    actionElement.setAttribute('disabled', 'disabled');

                    const batchFormFields = {
                        'batchActionName': actionElement.getAttribute('data-action-name'),
                        'entityFqcn': actionElement.getAttribute('data-entity-fqcn'),
                        'batchActionUrl': actionElement.getAttribute('data-action-url'),
                        'batchActionCsrfToken': actionElement.getAttribute('data-action-csrf-token'),
                    };
                    selectedItems.forEach((item, i) => {
                        batchFormFields[`batchActionEntityIds[${i}]`] = item.value;
                    });

                    const batchForm = document.createElement('form');
                    batchForm.setAttribute('method', 'POST');
                    batchForm.setAttribute('action', actionElement.getAttribute('data-action-url'));
                    for (let fieldName in batchFormFields) {
                        const formField = document.createElement('input');
                        formField.setAttribute('type', 'hidden');
                        formField.setAttribute('name', fieldName);
                        formField.setAttribute('value', batchFormFields[fieldName]);
                        batchForm.appendChild(formField);
                    }

                    document.body.appendChild(batchForm);
                    batchForm.submit();
                });
            });
        });
    }

    #createAutoCompleteFields() {
        const autocomplete = new Autocomplete();
        document.querySelectorAll('[data-ea-widget="ea-autocomplete"]').forEach((autocompleteElement) => {
            autocomplete.create(autocompleteElement);
        });
    }

    #createModalWindowsForDeleteActions() {
        document.querySelectorAll('.action-delete').forEach((actionElement) => {
            actionElement.addEventListener('click', (event) => {
                event.preventDefault();

                document.querySelector('#modal-delete-button').addEventListener('click', () => {
                    const deleteFormAction = actionElement.getAttribute('formaction');
                    const deleteForm = document.querySelector('#delete-form');
                    deleteForm.setAttribute('action', deleteFormAction);
                    deleteForm.submit();
                });
            });
        });
    }

    #createPopovers() {
        document.querySelectorAll('[data-bs-toggle="popover"]').forEach((popoverElement) => {
            new bootstrap.Popover(popoverElement);
        });
    }

    #createTooltips() {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((tooltipElement) => {
            new bootstrap.Tooltip(tooltipElement);
        });
    }

    #createFilterToggles() {
        document.querySelectorAll('.filter-checkbox').forEach((filterCheckbox) => {
            filterCheckbox.addEventListener('change', () => {
                const filterToggleLink = filterCheckbox.nextElementSibling;
                const filterExpandedAttribute = filterCheckbox.nextElementSibling.getAttribute('aria-expanded');

                if ((filterCheckbox.checked && 'false' === filterExpandedAttribute) || (!filterCheckbox.checked && 'true' === filterExpandedAttribute)) {
                    filterToggleLink.click();
                }
            });
        });

        document.querySelectorAll('form[data-ea-filters-form-id]').forEach((form) => {
            // TODO: when using the native datepicker, 'change' isn't fired unless you input the entire date + time information
            form.addEventListener('change', (event) => {
                if (event.target.classList.contains('filter-checkbox')) {
                    return;
                }

                const filterCheckbox = event.target.closest('.filter-field').querySelector('.filter-checkbox');
                if (!filterCheckbox.checked) {
                    filterCheckbox.checked = true;
                }
            });
        });

        document.querySelectorAll('[data-ea-comparison-id]').forEach((comparisonWidget) => {
            comparisonWidget.addEventListener('change', (event) => {
                const comparisonWidget = event.currentTarget;
                const comparisonId = comparisonWidget.dataset.eaComparisonId;

                if (comparisonId === undefined) {
                    return;
                }

                const secondValue = document.querySelector(`[data-ea-value2-of-comparison-id="${comparisonId}"]`);

                if (secondValue === null) {
                    return;
                }

                toggleVisibilityClasses(secondValue, comparisonWidget.value !== 'between');
            });
        });
    }
}
