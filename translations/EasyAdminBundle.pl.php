<?php

return [
    'page_title' => [
        // 'dashboard' => '',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Dodaj nowy %entity_label_singular%',
        'exception' => 'Błąd|Błędy|Błędy',
    ],

    'datagrid' => [
        'hidden_results' => 'Nie można wyświetlić niektórych wyników, ponieważ nie masz odpowiednich uprawnień',
        'no_results' => 'Brak wyników.',
    ],

    'paginator' => [
        'first' => 'Pierwsza',
        'previous' => 'Poprzednia',
        'next' => 'Następna',
        'last' => 'Ostatnia',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> z <strong>%results%</strong>',
        'results' => '<strong>%count%</strong> wynik|<strong>%count%</strong> wyniki|<strong>%count%</strong> wyników|{0} Brak wyników',
    ],

    'label' => [
        'true' => 'Tak',
        'false' => 'Nie',
        'empty' => 'Pusty',
        'null' => 'Brak',
        'object' => 'Obiekt PHP',
        'inaccessible' => 'Niedostępny',
        'inaccessible.explanation' => 'Metoda pobierająca (<i>ang. getter</i>) nie istnieje  dla tego pola lub właściwość (<i>ang. field</i>) nie jest publiczna',
        'form.empty_value' => 'Pusta wartość',
    ],

    'field' => [
        'code_editor.view_code' => 'Pokaż kod',
        'text_editor.view_content' => 'Pokaż zawartość',
    ],

    'action' => [
        'entity_actions' => 'Akcje',
        'new' => 'Dodaj %entity_label_singular%',
        'search' => 'Szukaj',
        'detail' => 'Pokaż',
        'edit' => 'Edytuj',
        'delete' => 'Usuń',
        'cancel' => 'Anuluj',
        'index' => 'Wróć do listy',
        'deselect' => 'Odznacz',
        'add_new_item' => 'Dodaj nową pozycję',
        'remove_item' => 'Usuń pozycję',
        'choose_file' => 'Wybierz plik',
        'close' => 'Zamknij',
        'create' => 'Dodaj',
        'create_and_add_another' => 'Zapisz i dodaj kolejny',
        'create_and_continue' => 'Zapisz i kontynuuj',
        'save' => 'Zapisz',
        'save_and_continue' => 'Zapisz i kontynuuj',
    ],

    'batch_action_modal' => [
        'title' => 'Czy na pewno chcesz zastosować do wybranych elementów?',
        'content' => 'Nie można cofnąć tej operacji.',
        'action' => 'Wykonaj',
    ],

    'delete_modal' => [
        'title' => 'Czy na pewno chcesz usunąć ten element?',
        'content' => 'Tej operacji nie można cofnąć.',
    ],

    'filter' => [
        'title' => 'Filtry',
        'button.clear' => 'Wyczyść',
        'button.apply' => 'Zatwierdź',
        'label.is_equal_to' => 'równe',
        'label.is_not_equal_to' => 'różne od',
        'label.is_greater_than' => 'większy niż',
        'label.is_greater_than_or_equal_to' => 'większy lub równy',
        'label.is_less_than' => 'mniejszy niż',
        'label.is_less_than_or_equal_to' => 'mniejszy lub równy',
        'label.is_between' => 'pomiędzy',
        'label.contains' => 'zawiera',
        'label.not_contains' => 'nie zawiera',
        'label.starts_with' => 'zaczyna się od',
        'label.ends_with' => 'kończy się na',
        'label.exactly' => 'dokładnie jak',
        'label.not_exactly' => 'nie dokładnie jak',
        'label.is_same' => 'takie samo jak',
        'label.is_not_same' => 'inne niż',
        'label.is_after' => 'późniejsza niż',
        'label.is_after_or_same' => 'taka sama lub późniejsza niż',
        'label.is_before' => 'wcześniejsza niż',
        'label.is_before_or_same' => 'taka sama lub wcześniejsza niż ',
    ],

    'form' => [
        'are_you_sure' => 'Nie zapisano zmian wprowadzonych w tym formularzu.',
        'tab.error_badge_title' => 'Wystąpił jeden błąd|Wystąpiły %count% błędy|Wystąpiło %count% błędów',
        'slug.confirm_text' => 'Jeśli zmienisz slug, linki mogą przestać działać na innych stronach.',
    ],

    'user' => [
        'logged_in_as' => 'Zalogowany jako',
        'unnamed' => 'Użytkownik bez nazwy',
        'anonymous' => 'Anonimowy użytkownik',
        'sign_out' => 'Wyloguj',
        'exit_impersonation' => 'Opuść tryb maskowania',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Wygląd',
            'light' => 'Jasny',
            'dark' => 'Ciemny',
            'auto' => 'Automatyczny',
        ],
        'locale' => 'Język',
    ],

    'login_page' => [
        'username' => 'Użytkownik',
        'password' => 'Hasło',
        'sign_in' => 'Zaloguj się',
        'forgot_password' => 'Nie pamiętasz hasła?',
        'remember_me' => 'Zapamiętaj mnie',
    ],

    'exception' => [
        'entity_not_found' => 'Ten obiekt nie jest już dostępny.',
        'entity_remove' => 'Ten obiekt nie może być usunięty ponieważ istnieją inne, które są z nim powiązane.',
        'forbidden_action' => 'Na tej pozycji nie można wykonać wybranej akcji.',
        'insufficient_entity_permission' => 'Nie masz uprawnień do tego obiektu.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Brak wyników',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Trwa ładowanie…',
    ],
];
