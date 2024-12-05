<?php

return [
    'page_title' => [
        'dashboard' => 'Nadzorna plošča',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Dodaj %entity_label_singular%',
        'exception' => '%count% napaka|%count% napaki|%count% napake|%count% napak',
    ],

    'datagrid' => [
        'hidden_results' => 'Nekaterih rezultatov ni mogoče prikazati, ker nimate ustreznih pravic',
        'no_results' => 'Nobenih rezultatov ni najdenih.',
    ],

    'paginator' => [
        'first' => 'Prva',
        'previous' => 'Prejšnja',
        'next' => 'Naslednja',
        'last' => 'Zadnja',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> od <strong>%results%</strong>',
        'results' => '{0} Ni rezultatov|{1} <strong>1</strong> rezultat|{2} <strong>2</strong> rezultata|{3,4} <strong>%count%</strong> rezultati|[5,Inf] <strong>%count%</strong> rezultatov',
    ],

    'label' => [
        'true' => 'Da',
        'false' => 'Ne',
        'empty' => 'Prazno',
        'null' => 'Null',
        'object' => 'PHP objekt',
        'inaccessible' => 'Nedostopno',
        'inaccessible.explanation' => 'Getter metoda ne obstaja za to polje ali pa lastnost ni javna',
        'form.empty_value' => 'Noben',
    ],

    'field' => [
        'code_editor.view_code' => 'Ogled kode',
        'text_editor.view_content' => 'Ogled vsebine',
    ],

    'action' => [
        'entity_actions' => 'Dejanja',
        'new' => 'Dodaj %entity_label_singular%',
        'search' => 'Iskanje',
        'detail' => 'Prikaži',
        'edit' => 'Uredi',
        'delete' => 'Izbriši',
        'cancel' => 'Prekliči',
        'index' => 'Nazaj na seznam',
        'deselect' => 'Preklic izbire',
        'add_new_item' => 'Dodaj nov element',
        'remove_item' => 'Odstrani element',
        'choose_file' => 'Izberite datoteko',
        'close' => 'Zapri',
        'create' => 'Ustvari',
        'create_and_add_another' => 'Ustvari in dodaj drugega',
        'create_and_continue' => 'Ustvari in nadaljuj urejanje',
        'save' => 'Shrani spremembe',
        'save_and_continue' => 'Shrani in nadaljuj urejanje',
    ],

    'batch_action_modal' => [
        'title' => 'Uporabili boste dejanje "%action_name%" za %num_items% element(ov).',
        'content' => 'Za to operacijo ni razveljavitve.',
        'action' => 'Nadaljuj',
    ],

    'delete_modal' => [
        'title' => 'Ali res želite izbrisati ta element?',
        'content' => 'Razveljavitev za to operacijo ne obstaja.',
    ],

    'filter' => [
        'title' => 'Filtri',
        'button.clear' => 'Počisti',
        'button.apply' => 'Uporabi',
        'label.is_equal_to' => 'je enako',
        'label.is_not_equal_to' => 'ni enako',
        'label.is_greater_than' => 'je večje od',
        'label.is_greater_than_or_equal_to' => 'je večje ali enako',
        'label.is_less_than' => 'je manjše od',
        'label.is_less_than_or_equal_to' => 'je manjše od ali enako',
        'label.is_between' => 'je med',
        'label.contains' => 'vsebuje',
        'label.not_contains' => 'ne vsebuje',
        'label.starts_with' => 'se začne',
        'label.ends_with' => 'se konča',
        'label.exactly' => 'točno',
        'label.not_exactly' => 'ni točno',
        'label.is_same' => 'je enako',
        'label.is_not_same' => 'ni enako',
        'label.is_after' => 'je za',
        'label.is_after_or_same' => 'je za ali enako',
        'label.is_before' => 'je pred',
        'label.is_before_or_same' => 'je pred ali enako',
    ],

    'form' => [
        'are_you_sure' => 'Sprememb, ki ste jih naredili na tem obrazcu, niste shranili.',
        'tab.error_badge_title' => '{1} En neveljaven vnos|{2} 2 neveljavna vnosa|{3,4} %count% neveljavni vnosi|[5,Inf] %count% neveljavnih vnosov',
        'slug.confirm_text' => 'Če spremenite naslov, lahko prelomite povezave na druge strani.',
    ],

    'user' => [
        'logged_in_as' => 'Prijavljeni kot',
        'unnamed' => 'Neimenovani uporabnik',
        'anonymous' => 'Anonimni uporabnik',
        'sign_out' => 'Odjava',
        'exit_impersonation' => 'Izhod iz poosebljanja',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Videz',
            'light' => 'Svetlo',
            'dark' => 'Temno',
            'auto' => 'Avtomatsko',
        ],
        'locale' => 'Jezik',
    ],

    'login_page' => [
        'username' => 'Uporabniško ime',
        'password' => 'Geslo',
        'sign_in' => 'Prijava',
        'forgot_password' => 'Ste pozabili geslo?',
        'remember_me' => 'Zapomni si me',
    ],

    'exception' => [
        'entity_not_found' => 'Ta element ni več na voljo.',
        'entity_remove' => 'Tega elementac ni mogoče izbrisati, ker so ostali elementi odvisni od njega.',
        'forbidden_action' => 'Zahtevanega dejanja ni mogoče izvršiti na tem elementu.',
        'insufficient_entity_permission' => 'Za dostop do tega elementa nimate ustreznih pravic.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Ni zadetkov',
        'no-more-results' => 'Ni več rezultatov',
        'loading-more-results' => 'Nalagam več zadetkov …',
    ],
];
