<?php

return [
    'page_title' => [
        'dashboard' => 'Дешборд',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Створити новий %entity_label_singular%',
        'exception' => 'Помилка|Помилки',
    ],

    'datagrid' => [
        'hidden_results' => 'Деякі результати можуть не відображатися, оскільки у вас недостатньо прав',
        'no_results' => 'Нічого не знайдено.',
    ],

    'paginator' => [
        'first' => 'Перша',
        'previous' => 'Попередня',
        'next' => 'Наступна',
        'last' => 'Остання',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> з <strong>%results%</strong>',
        'results' => '{0} Немає результатів|{1} <strong>1</strong> результат|{2,3,4} <strong>%count%</strong> результати|[5,Inf] <strong>%count%</strong> результатів',
    ],

    'label' => [
        'true' => 'Так',
        'false' => 'Ні',
        'empty' => 'Порожньо',
        'null' => 'Null',
        'object' => 'PHP Об\'єкт',
        'inaccessible' => 'Недоступно',
        'inaccessible.explanation' => 'Немає геттера для цього поля або поле не публічне',
        'form.empty_value' => 'Пусто',
    ],

    'field' => [
        'code_editor.view_code' => 'Переглянути код',
        'text_editor.view_content' => 'Переглянути вміст',
    ],

    'action' => [
        'entity_actions' => 'Дії',
        'new' => 'Створити %entity_label_singular%',
        'search' => 'Пошук',
        'detail' => 'Показати',
        'edit' => 'Редагувати',
        'delete' => 'Видалити',
        'cancel' => 'Відмінити',
        'index' => 'Повернутися до списку',
        'deselect' => 'Зняти вибір',
        'add_new_item' => 'Додати новий елемент',
        'remove_item' => 'Видалити елемент',
        'choose_file' => 'Вибрати файл',
        'close' => 'Закрити',
        'create' => 'Створити',
        'create_and_add_another' => 'Створити і додати ще',
        'create_and_continue' => 'Створити і продовжити',
        'save' => 'Зберегти',
        'save_and_continue' => 'Зберегти і продовжити',
    ],

    'batch_action_modal' => [
        'title' => 'Ви дійсно хочете змінити вибрані елементи?',
        'content' => 'Цю операцію неможна відмінити.',
        'action' => 'Продовжити',
    ],

    'delete_modal' => [
        'title' => 'Ви дійсно бажаєте видалити цей об\'єкт?',
        'content' => 'Цю дію не можна буде відмінити.',
    ],

    'filter' => [
        'title' => 'Фільтри',
        'button.clear' => 'Очистити',
        'button.apply' => 'Застосувати',
        'label.is_equal_to' => 'рівно',
        'label.is_not_equal_to' => 'не рівно',
        'label.is_greater_than' => 'більше ніж',
        'label.is_greater_than_or_equal_to' => 'більше ніж або рівно',
        'label.is_less_than' => 'менше ніж',
        'label.is_less_than_or_equal_to' => 'менше ніж або рівно',
        'label.is_between' => 'між',
        'label.contains' => 'містить',
        'label.not_contains' => 'не містить',
        'label.starts_with' => 'починається з',
        'label.ends_with' => 'закінчується на',
        'label.exactly' => 'точно',
        'label.not_exactly' => 'не точно',
        'label.is_same' => 'так само',
        'label.is_not_same' => 'не так само',
        'label.is_after' => 'після',
        'label.is_after_or_same' => 'після або ж',
        'label.is_before' => 'до',
        'label.is_before_or_same' => 'до або ж',
    ],

    'form' => [
        'are_you_sure' => 'Ви не зберегли зроблені зміни.',
        'tab.error_badge_title' => 'Невалідне поле|Невалідних полів: %count%',
        'slug.confirm_text' => 'Якщо ви змінете текстовий ідентифікатор, ви можете зламати посилання на інших сторінках.',
    ],

    'user' => [
        'logged_in_as' => 'Ви ввійшли як',
        'unnamed' => 'Безіменний користувач',
        'anonymous' => 'Анонімний користувач',
        'sign_out' => 'Вихід',
        'exit_impersonation' => 'Вийти з-під користувача',
    ],

    'login_page' => [
        'username' => 'Логін',
        'password' => 'Пароль',
        'sign_in' => 'Увійти',
        'forgot_password' => 'Забули Пароль?',
        'remember_me' => 'Запам\'ятати Мене',
    ],

    'exception' => [
        'entity_not_found' => 'Елемент більше недоступний.',
        'entity_remove' => 'Цей елемент не можна видалити, оскільки від нього залежать інші елементи.',
        'forbidden_action' => 'Дія не може бути виконана над цим елементом.',
        'insufficient_entity_permission' => 'У вас немає дозволу на доступ до цього елемента.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Нічого не знайдено',
        'no-more-results' => 'Більше немає результатів',
        'loading-more-results' => 'Завантаження інших результатів…',
    ],
];
