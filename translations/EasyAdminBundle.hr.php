<?php

return [
    'page_title' => [
        // 'dashboard' => '',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Uredi %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Izradi %entity_label_singular%',
        'exception' => 'Greška|Greške',
    ],

    'datagrid' => [
        // 'hidden_results' => '',
        'no_results' => 'Nema rezultata pretrage.',
    ],

    'paginator' => [
        'first' => 'Prvi',
        'previous' => 'Prethodan',
        'next' => 'Sljedeći',
        'last' => 'Posljednji',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> od <strong>%results%</strong>',
        // 'results' => '',
    ],

    'label' => [
        'true' => 'Da',
        'false' => 'Ne',
        'empty' => 'Prazno',
        'null' => 'Null',
        'object' => 'PHP Object',
        'inaccessible' => 'Nepristupačan',
        'inaccessible.explanation' => 'Getter metoda ne postoji za ovo polje ili vrijednost svojstva nije javna',
        'form.empty_value' => 'Prazno',
    ],

    'field' => [
        // 'code_editor.view_code' => '',
        // 'text_editor.view_content' => '',
    ],

    'action' => [
        'entity_actions' => 'Akcije',
        'new' => 'Dodaj %entity_label_singular%',
        'search' => 'Pretraži',
        'detail' => 'Prikaži',
        'edit' => 'Uredi',
        'delete' => 'Izbriši',
        'cancel' => 'Poništi',
        'index' => 'Natrag na popis',
        // 'deselect' => '',
        'add_new_item' => 'Dodajte novu stavku',
        'remove_item' => 'Uklonite stavku',
        'choose_file' => 'Odaberi datoteku',
        // 'close' => '',
        // 'create' => '',
        // 'create_and_add_another' => '',
        // 'create_and_continue' => '',
        // 'save' => '',
        // 'save_and_continue' => '',
    ],

    'batch_action_modal' => [
        // 'title' => '',
        // 'content' => '',
        // 'action' => '',
    ],

    'delete_modal' => [
        'title' => 'Jeste li sigurni da želite izbrisati ovu stavku?',
        'content' => 'Izbrisana stavka se ne može povratiti',
    ],

    'filter' => [
        // 'title' => '',
        // 'button.clear' => '',
        // 'button.apply' => '',
        // 'label.is_equal_to' => '',
        // 'label.is_not_equal_to' => '',
        // 'label.is_greater_than' => '',
        // 'label.is_greater_than_or_equal_to' => '',
        // 'label.is_less_than' => '',
        // 'label.is_less_than_or_equal_to' => '',
        // 'label.is_between' => '',
        // 'label.contains' => '',
        // 'label.not_contains' => '',
        // 'label.starts_with' => '',
        // 'label.ends_with' => '',
        // 'label.exactly' => '',
        // 'label.not_exactly' => '',
        // 'label.is_same' => '',
        // 'label.is_not_same' => '',
        // 'label.is_after' => '',
        // 'label.is_after_or_same' => '',
        // 'label.is_before' => '',
        // 'label.is_before_or_same' => '',
    ],

    'form' => [
        'are_you_sure' => 'Niste spremili izmjene na ovom obrascu.',
        // 'tab.error_badge_title' => '',
    ],

    'user' => [
        'logged_in_as' => 'Prijavljen kao',
        'unnamed' => 'Neimenovani korisnik',
        'anonymous' => 'Anonimni korisnik',
        'sign_out' => 'Odjava',
        // 'exit_impersonation' => '',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Izgled',
            'light' => 'Svijetli',
            'dark' => 'Tamni',
            'auto' => 'Automatski',
        ],
        'locale' => 'Jezik',
    ],

    'login_page' => [
        'username' => 'Korisničko ime',
        'password' => 'Lozinka',
        'sign_in' => 'Prijavi se',
        'forgot_password' => 'Zaboravili ste Vašu lozinku?',
        'remember_me' => 'Zapamti me',
    ],

    'exception' => [
        'entity_not_found' => 'Ta stavka više nije dostupna.',
        'entity_remove' => 'Ta stavka ne može se izbrisati jer ovise o njoj ostale stavke.',
        'forbidden_action' => 'Zatražena radnja ne može se izvršiti na ovoj stavci.',
        // 'insufficient_entity_permission' => 'You don't have permission to access this item.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Nema rezultata',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Učitavanje rezultata…',
    ],
];
