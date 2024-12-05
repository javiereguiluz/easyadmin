<?php

return [
    'page_title' => [
        'dashboard' => 'Табло',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Редактиране на %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Създаване на %entity_label_singular%',
        'exception' => 'Грешка|Грешки',
    ],

    'datagrid' => [
        'hidden_results' => 'Някои резултати не могат да бъдат показани, защото нямате необходимите права за това',
        'no_results' => 'Няма резултати.',
    ],

    'paginator' => [
        'first' => 'Първа',
        'previous' => 'Предишна',
        'next' => 'Следваща',
        'last' => 'Последна',
        'counter' => '<strong>%start%</strong>–<strong>%end%</strong> от <strong>%results%</strong>',
        'results' => '{0} Няма резултати|{1} <strong>1</strong> резултат|]1,Inf] <strong>%count%</strong> резултата',
    ],

    'label' => [
        'true' => 'Да',
        'false' => 'Не',
        'empty' => 'Празно',
        'null' => 'Null',
        'object' => 'PHP обект',
        'inaccessible' => 'Недостъпно',
        'inaccessible.explanation' => 'Не съществува getter метод за това поле или то не е публично',
        'form.empty_value' => 'None',
    ],

    'field' => [
        'code_editor.view_code' => 'Преглед на кода',
        'text_editor.view_content' => 'Преглед на съдържание',
    ],

    'action' => [
        'entity_actions' => 'Действия',
        'new' => 'Добавяне на %entity_label_singular%',
        'search' => 'Търсене',
        'detail' => 'Преглед',
        'edit' => 'Редактиране',
        'delete' => 'Изтриване',
        'cancel' => 'Отказ',
        'index' => 'Обратно към списъка',
        'deselect' => 'Премахване на избора',
        'add_new_item' => 'Добавяне на нов запис',
        'remove_item' => 'Изтриване на записа',
        'choose_file' => 'Избор на файл',
        'close' => 'Затвори',
        'create' => 'Създай',
        'create_and_add_another' => 'Създай и добави нов',
        'create_and_continue' => 'Създай и продължи редакцията',
        'save' => 'Запиши',
        'save_and_continue' => 'Запиши и продължи редакцията',
    ],

    'batch_action_modal' => [
        'title' => 'Ще приложите действието "%action_name%" към %num_items% запис(а).',
        'content' => 'Не можете да отмените това действие.',
        'action' => 'Извърши',
    ],

    'delete_modal' => [
        'title' => 'Наистина ли желаете да изтриете записа?',
        'content' => 'Това действие е необратимо.',
    ],

    'filter' => [
        'title' => 'Филтри',
        'button.clear' => 'Изчисти',
        'button.apply' => 'Приложи',
        'label.is_equal_to' => 'е равно на',
        'label.is_not_equal_to' => 'не е равно на',
        'label.is_greater_than' => 'е по-голямо от',
        'label.is_greater_than_or_equal_to' => 'е по-голямо или равно на',
        'label.is_less_than' => 'е по-малко от',
        'label.is_less_than_or_equal_to' => 'е по-малко или равно на',
        'label.is_between' => 'е между',
        'label.contains' => 'съдържа',
        'label.not_contains' => 'не съдържа',
        'label.starts_with' => 'започва с',
        'label.ends_with' => 'завършва със',
        'label.exactly' => 'точно',
        'label.not_exactly' => 'не точно',
        'label.is_same' => 'е същото',
        'label.is_not_same' => 'не е същото',
        'label.is_after' => 'е след',
        'label.is_after_or_same' => 'е след или същото',
        'label.is_before' => 'е преди',
        'label.is_before_or_same' => 'е преди или същото',
    ],

    'form' => [
        'are_you_sure' => 'Не сте запазили направените в страницата промени.',
        'tab.error_badge_title' => 'Едно невалидно поле|%count% невалидни полета',
        'slug.confirm_text' => 'Ако промените адреса, можете да счупите връзките от други страници.',
    ],

    'user' => [
        'logged_in_as' => 'Влезли сте като',
        'unnamed' => 'Безименен потребител',
        'anonymous' => 'Анонимен потребител',
        'sign_out' => 'Изход',
        'exit_impersonation' => 'Обратно към вашия профил',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Изглед',
            'light' => 'Светъл',
            'dark' => 'Тъмен',
            'auto' => 'Автоматичен',
        ],
        'locale' => 'език',
    ],

    'login_page' => [
        'username' => 'Потребителско име',
        'password' => 'Парола',
        'sign_in' => 'Вход',
        'forgot_password' => 'Забравена парола?',
        'remember_me' => 'Запомни ме',
    ],

    'exception' => [
        'entity_not_found' => 'Този запис вече не е налице.',
        'entity_remove' => 'Този запис не може да бъде изтрит, защото други записи зависят от него.',
        'forbidden_action' => 'Заявеното действие не може да се изпълни за този запис.',
        'insufficient_entity_permission' => 'Нямате разрешение за достъп до този запис.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Няма намерени резултати',
        'no-more-results' => 'Няма повече резултати',
        'loading-more-results' => 'Зареждат се още…',
    ],
];
