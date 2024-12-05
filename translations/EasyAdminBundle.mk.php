<?php

return [
    'page_title' => [
        'dashboard' => 'Контролна табла',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Уреди %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Креирај %entity_label_singular%',
        'exception' => 'Грешка|Грешки',
    ],

    'datagrid' => [
        'hidden_results' => 'Некои резултати нема да бидат прикажани бидејќи ги немате потребните привилегии',
        'no_results' => 'Нема пронајдено резултати.',
    ],

    'paginator' => [
        'first' => 'Прва',
        'previous' => 'Претходна',
        'next' => 'Следна',
        'last' => 'Последна',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> од <strong>%results%</strong>',
        'results' => '{0} Нема пронајдено резултати|{1} <strong>1</strong> резултат|]1,Inf] <strong>%count%</strong> резултати',
    ],

    'label' => [
        'true' => 'Да',
        'false' => 'Не',
        'empty' => 'Празно',
        'null' => 'Нема вредност (null)',
        'object' => 'PHP Објект',
        'inaccessible' => 'Недостапно',
        'inaccessible.explanation' => 'Getter методот не постои за ова поле или полето нема јавен пристап - is not public',
        'form.empty_value' => 'Ништо',
    ],

    'field' => [
        'code_editor.view_code' => 'Види го кодот',
        'text_editor.view_content' => 'Види ја содржината',
    ],

    'action' => [
        'entity_actions' => 'Акции',
        'new' => 'Додади %entity_label_singular%',
        'search' => 'Пребарај',
        'detail' => 'Покажи',
        'edit' => 'Уреди',
        'delete' => 'Избриши',
        'cancel' => 'Откажи',
        'index' => 'Назад кон листата',
        'deselect' => 'Поништи го изборот',
        'add_new_item' => 'Додади запис',
        'remove_item' => 'Избриши запис',
        'choose_file' => 'Одбери датотека',
        'close' => 'Затвори',
        'create' => 'Креирај',
        'create_and_add_another' => 'Креирај и додади ново',
        'create_and_continue' => 'Креирај и продолжи со уредување',
        'save' => 'Зачувај ги промените',
        'save_and_continue' => 'Зачувај и продолжи со уредување',
    ],

    'batch_action_modal' => [
        'title' => 'Ке го примените дејствието "%action_name%" на %num_items% запис(и).',
        'content' => 'За оваа операција нема поништување.',
        'action' => 'Продолжи',
    ],

    'delete_modal' => [
        'title' => 'Дали навистина сакате да го избришете овој запис?',
        'content' => 'За оваа операција нема поништување.',
    ],

    'filter' => [
        'title' => 'Филтри',
        'button.clear' => 'Откажи ги филтрите',
        'button.apply' => 'Примени',
        'label.is_equal_to' => 'е еднакво на',
        'label.is_not_equal_to' => 'не е еднакво на',
        'label.is_greater_than' => 'е поголемо од',
        'label.is_greater_than_or_equal_to' => 'е поголемо или еднакво со',
        'label.is_less_than' => 'е помало од',
        'label.is_less_than_or_equal_to' => 'е помало или еднакво со',
        'label.is_between' => 'е помеѓу',
        'label.contains' => 'содржи',
        'label.not_contains' => 'не содржи',
        'label.starts_with' => 'започнува со',
        'label.ends_with' => 'завршува со',
        'label.exactly' => 'точно',
        'label.not_exactly' => 'не е точно',
        'label.is_same' => 'е исто',
        'label.is_not_same' => 'не е исто',
        'label.is_after' => 'е после',
        'label.is_after_or_same' => 'е после или исто',
        'label.is_before' => 'е пред',
        'label.is_before_or_same' => 'е пред или исто',
    ],

    'form' => [
        'are_you_sure' => 'Измените направени во формата не се зачувани.',
        'tab.error_badge_title' => 'Еден погрешен внес|%count% погрешни внесови',
        'slug.confirm_text' => 'Ако го смените автоматски креираниот наслов (slug), ке ги прекинете линковоте кон другите страни.',
    ],

    'user' => [
        'logged_in_as' => 'Најавен како',
        'unnamed' => 'Неименуван корисник',
        'anonymous' => 'Анонимен корисник',
        'sign_out' => 'Одјавете се',
        'exit_impersonation' => 'Прекин на користење на туѓо корисничко име',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Изглед',
            'light' => 'Светол',
            'dark' => 'Темен',
            'auto' => 'Автоматски',
        ],
        'locale' => 'Јазик',
    ],

    'login_page' => [
        'username' => 'Корисничко име',
        'password' => 'Лозинка',
        'sign_in' => 'Најавете се',
        'forgot_password' => 'Ја заборавивте лозинката?',
        'remember_me' => 'Запомни ме',
    ],

    'exception' => [
        'entity_not_found' => 'Овој запис повеќе не е достапен.',
        'entity_remove' => 'Овој запис не може да биде избришан затоа што други записи зависат од него.',
        'forbidden_action' => 'Баранато дејствие не може да се изврши на овој запис.',
        'insufficient_entity_permission' => 'Ги немате неопходните привилегии за да пристапите до овој запис.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Нема пронајдено резултати',
        'no-more-results' => 'Нема веќе резултати',
        'loading-more-results' => 'Се вчитуваат повеке резултати…',
    ],
];
