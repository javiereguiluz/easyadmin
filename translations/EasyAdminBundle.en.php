<?php

return [
    'page_title' => [
        'dashboard' => 'Dashboard',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Edit %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Create %entity_label_singular%',
        'exception' => 'Error|Errors',
    ],

    'datagrid' => [
        'hidden_results' => 'Some results can\'t be displayed because you don\'t have enough permissions',
        'no_results' => 'No results found.',
    ],

    'paginator' => [
        'first' => 'First',
        'previous' => 'Previous',
        'next' => 'Next',
        'last' => 'Last',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> of <strong>%results%</strong>',
        'results' => '{0} No results|{1} <strong>1</strong> result|]1,Inf] <strong>%count%</strong> results',
    ],

    'label' => [
        'true' => 'Yes',
        'false' => 'No',
        'empty' => 'Empty',
        'null' => 'Null',
        'object' => 'PHP Object',
        'inaccessible' => 'Inaccessible',
        'inaccessible.explanation' => 'Getter method does not exist for this field or the field is not public',
        'form.empty_value' => 'None',
    ],

    'field' => [
        'code_editor.view_code' => 'View code',
        'text_editor.view_content' => 'View content',
    ],

    'action' => [
        'entity_actions' => 'Actions',
        'new' => 'Add %entity_label_singular%',
        'search' => 'Search',
        'detail' => 'Show',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'cancel' => 'Cancel',
        'index' => 'Back to listing',
        'deselect' => 'Deselect',
        'add_new_item' => 'Add a new item',
        'remove_item' => 'Remove the item',
        'choose_file' => 'Choose file',
        'close' => 'Close',
        'create' => 'Create',
        'create_and_add_another' => 'Create and add another',
        'create_and_continue' => 'Create and continue editing',
        'save' => 'Save changes',
        'save_and_continue' => 'Save and continue editing',
    ],

    'batch_action_modal' => [
        'title' => 'You are going to apply the "%action_name%" action to %num_items% item(s).',
        'content' => 'There is no undo for this operation.',
        'action' => 'Proceed',
    ],

    'delete_modal' => [
        'title' => 'Do you really want to delete this item?',
        'content' => 'There is no undo for this operation.',
    ],

    'filter' => [
        'title' => 'Filters',
        'button.clear' => 'Clear',
        'button.apply' => 'Apply',
        'label.is_equal_to' => 'is equal to',
        'label.is_not_equal_to' => 'is not equal to',
        'label.is_greater_than' => 'is greater than',
        'label.is_greater_than_or_equal_to' => 'is greater than or equal to',
        'label.is_less_than' => 'is less than',
        'label.is_less_than_or_equal_to' => 'is less than or equal to',
        'label.is_between' => 'is between',
        'label.contains' => 'contains',
        'label.not_contains' => 'doesn\'t contain',
        'label.starts_with' => 'starts with',
        'label.ends_with' => 'ends with',
        'label.exactly' => 'exactly',
        'label.not_exactly' => 'not exactly',
        'label.is_same' => 'is same',
        'label.is_not_same' => 'is not same',
        'label.is_after' => 'is after',
        'label.is_after_or_same' => 'is after or same',
        'label.is_before' => 'is before',
        'label.is_before_or_same' => 'is before or same',
    ],

    'form' => [
        'are_you_sure' => 'You haven\'t saved the changes made on this form.',
        'tab.error_badge_title' => 'One invalid input|%count% invalid inputs',
        'slug.confirm_text' => 'If you change the slug, you can break links on other pages.',
    ],

    'user' => [
        'logged_in_as' => 'Logged in as',
        'unnamed' => 'Unnamed User',
        'anonymous' => 'Anonymous User',
        'sign_out' => 'Sign out',
        'exit_impersonation' => 'Exit impersonation',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Appearance',
            'light' => 'Light',
            'dark' => 'Dark',
            'auto' => 'Auto',
        ],
        'locale' => 'Language',
    ],

    'login_page' => [
        'username' => 'Username',
        'password' => 'Password',
        'sign_in' => 'Sign in',
        'forgot_password' => 'Forgot Your Password?',
        'remember_me' => 'Remember me',
    ],

    'exception' => [
        'entity_not_found' => 'This item is no longer available.',
        'entity_remove' => 'This item can\'t be deleted because other items depend on it.',
        'forbidden_action' => 'The requested action can\'t be performed on this item.',
        'insufficient_entity_permission' => 'You don\'t have permission to access this item.',
    ],

    'autocomplete' => [
        'no-results-found' => 'No results found',
        'no-more-results' => 'No more results',
        'loading-more-results' => 'Loading more results…',
    ],
];
