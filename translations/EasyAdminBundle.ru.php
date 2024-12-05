<?php

return [
    'page_title' => [
        'dashboard' => 'Дашборд',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Создать новый %entity_label_singular%',
        'exception' => 'Ошибка|Ошибки|Ошибок',
    ],

    'datagrid' => [
        'hidden_results' => 'Некоторые результаты не могут быть отображены, потому что вы не имеете достаточных привелегий',
        'no_results' => 'Ничего не найдено.',
    ],

    'paginator' => [
        'first' => 'Первая',
        'previous' => 'Предыдущая',
        'next' => 'Следующая',
        'last' => 'Последняя',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> из <strong>%results%</strong>',
        'results' => '<strong>%count%</strong> результат|<strong>%count%</strong> результата|<strong>%count%</strong> результатов',
    ],

    'label' => [
        'true' => 'Да',
        'false' => 'Нет',
        'empty' => 'Пусто',
        'null' => 'Null',
        'object' => 'PHP-объект',
        'inaccessible' => 'Недоступно',
        'inaccessible.explanation' => 'Нет геттера для этого поля или свойство не общедоступно',
        'form.empty_value' => 'Пусто',
    ],

    'field' => [
        'code_editor.view_code' => 'Просмотреть код',
        'text_editor.view_content' => 'Просмотреть содержимое',
    ],

    'action' => [
        'entity_actions' => 'Действия',
        'new' => 'Создать %entity_label_singular%',
        'search' => 'Поиск',
        'detail' => 'Показать',
        'edit' => 'Редактировать',
        'delete' => 'Удалить',
        'cancel' => 'Отклонить',
        'index' => 'Вернуться к списку',
        'deselect' => 'Снять выбор',
        'add_new_item' => 'Добавить новый элемент',
        'remove_item' => 'Удалить элемент',
        'choose_file' => 'Выберите файл',
        'close' => 'Закрыть',
        'create' => 'Создать',
        'create_and_add_another' => 'Создать и добавить еще',
        'create_and_continue' => 'Создать и продолжить',
        'save' => 'Сохранить',
        'save_and_continue' => 'Сохранить и продолжить',
    ],

    'batch_action_modal' => [
        'title' => 'Вы собираетесь выполнить действие "%action_name%" для выбранных строк (%num_items%)',
        'content' => 'Эту операцию нельзя отменить.',
        'action' => 'Продолжить',
    ],

    'delete_modal' => [
        'title' => 'Вы действительно хотите удалить этот объект?',
        'content' => 'Эту операцию нельзя отменить.',
    ],

    'filter' => [
        'title' => 'Фильтры',
        'button.clear' => 'Очистить',
        'button.apply' => 'Применить',
        'label.is_equal_to' => 'равно',
        'label.is_not_equal_to' => 'не равно',
        'label.is_greater_than' => 'больше чем',
        'label.is_greater_than_or_equal_to' => 'больше или равно',
        'label.is_less_than' => 'меньше чем',
        'label.is_less_than_or_equal_to' => 'меньше или равно',
        'label.is_between' => 'между',
        'label.contains' => 'содержит',
        'label.not_contains' => 'не содержит',
        'label.starts_with' => 'начинается с',
        'label.ends_with' => 'заканчивается на',
        'label.exactly' => 'точно',
        'label.not_exactly' => 'не точно',
        'label.is_same' => 'такой же',
        'label.is_not_same' => 'не такой же',
        'label.is_after' => 'после',
        'label.is_after_or_same' => 'после или соответствует',
        'label.is_before' => 'до',
        'label.is_before_or_same' => 'до или соответствует',
    ],

    'form' => [
        'are_you_sure' => 'Вы не сохранили сделанные изменения.',
        'tab.error_badge_title' => 'Один неверный ввод|%count% неверных ввода|%count% неверных вводов',
        'slug.confirm_text' => 'Если вы измените текстовый идентификатор, вы можете сломать ссылки на других страницах.',
    ],

    'user' => [
        'logged_in_as' => 'Вы вошли как',
        'unnamed' => 'Безымянный пользователь',
        'anonymous' => 'Анонимный пользователь',
        'sign_out' => 'Выход',
        'exit_impersonation' => 'Выйти из-под пользователя',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Оформление',
            'light' => 'Светлое',
            'dark' => 'Темное',
            'auto' => 'Автоматически',
        ],
        'locale' => 'Язык',
    ],

    'login_page' => [
        'username' => 'Логин',
        'password' => 'Пароль',
        'sign_in' => 'Войти',
        'forgot_password' => 'Забыли пароль?',
        'remember_me' => 'Запомнить меня',
    ],

    'exception' => [
        'entity_not_found' => 'Элемент больше не доступен.',
        'entity_remove' => 'Элемент не может быть удалён, потому что другой элемент зависит от него.',
        'forbidden_action' => 'Запрашиваемое действие запрещено для этого элемента.',
        'insufficient_entity_permission' => 'У вас недостаточно прав для доступа к этому элементу.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Совпадений не найдено',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Загрузка данных…',
    ],
];
