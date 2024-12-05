<?php

return [
    'page_title' => [
        'dashboard' => 'Úvod',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Editovat %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Vytvořit %entity_label_singular%',
        'exception' => 'Chyba|Chyby',
    ],

    'datagrid' => [
        'hidden_results' => 'Některé položky nemohly být zobrazeny neboť nemáte patřičná oprávnění',
        'no_results' => 'Žádné položky.',
    ],

    'paginator' => [
        'first' => 'První',
        'previous' => 'Předchozí',
        'next' => 'Další',
        'last' => 'Poslední',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> z <strong>%results%</strong>',
        'results' => '{0} Žádná položka|{1} <strong>1</strong> položka|{2,3,4} <strong>%count%</strong> položky|[5,Inf] <strong>%count%</strong> položek',
    ],

    'label' => [
        'true' => 'Ano',
        'false' => 'Ne',
        'empty' => 'Prázdné',
        'null' => 'Nulové',
        'object' => 'PHP objekt',
        'inaccessible' => 'Nepřístupné',
        'inaccessible.explanation' => 'Getter metoda pro toto pole neexistuje nebo není veřejná',
        'form.empty_value' => 'Prázdné',
    ],

    'field' => [
        'code_editor.view_code' => 'Zobrazit kód',
        'text_editor.view_content' => 'Zobrazit obsah',
    ],

    'action' => [
        'entity_actions' => 'Akce',
        'new' => 'Vytvořit %entity_label_singular%',
        'search' => 'Hledat',
        'detail' => 'Zobrazit',
        'edit' => 'Upravit',
        'delete' => 'Smazat',
        'cancel' => 'Zrušit',
        'index' => 'Zpět na výpis',
        'deselect' => 'Zrušit označení',
        'add_new_item' => 'Vložit položku',
        'remove_item' => 'Odstranit položku',
        'choose_file' => 'Vybrat soubor',
        'close' => 'Zavřít',
        'create' => 'Vytvořit',
        'create_and_add_another' => 'Vytvořit a přidat další',
        'create_and_continue' => 'Vytvořit a pokračovat',
        'save' => 'Uložit',
        'save_and_continue' => 'Uložit a pokračovat',
    ],

    'batch_action_modal' => [
        'title' => 'Opravdu chcete upravit vybrané položky?',
        'content' => 'Tuto akci není možné vrátit zpět.',
        'action' => 'Pokračovat',
    ],

    'delete_modal' => [
        'title' => 'Opravdu chcete smazat tuto položku?',
        'content' => 'Tuto akci není možné vrátit zpět.',
    ],

    'filter' => [
        'title' => 'Filtry',
        'button.clear' => 'Zrušit',
        'button.apply' => 'Aplikovat',
        'label.is_equal_to' => 'rovná se',
        'label.is_not_equal_to' => 'nerovná se',
        'label.is_greater_than' => 'je větší než',
        'label.is_greater_than_or_equal_to' => 'je větší než nebo rovno',
        'label.is_less_than' => 'je menší než',
        'label.is_less_than_or_equal_to' => 'je menší než nebo rovno',
        'label.is_between' => 'je mezi',
        'label.contains' => 'obsahuje',
        'label.not_contains' => 'neobsahuje',
        'label.starts_with' => 'začíná na',
        'label.ends_with' => 'končí na',
        'label.exactly' => 'je přesně',
        'label.not_exactly' => 'není přesně',
        'label.is_same' => 'je stejné',
        'label.is_not_same' => 'není stejné',
        'label.is_after' => 'je po',
        'label.is_after_or_same' => 'je po nebo stejně',
        'label.is_before' => 'je před',
        'label.is_before_or_same' => 'je před nebo stejně',
    ],

    'form' => [
        'are_you_sure' => 'Neuložili jste změny provedené v tomto formuláři.',
        'tab.error_badge_title' => '{1} Jeden neplatný vstup|{2,3,4} %count% neplatné vstupy|[5,Inf] %count% neplatných vstupů',
    ],

    'user' => [
        'logged_in_as' => 'Přihlášen jako',
        'unnamed' => 'Nepojmenovaný uživatel',
        'anonymous' => 'Anonymní uživatel',
        'sign_out' => 'Odhlásit se',
        'exit_impersonation' => 'Ukončit impersonaci',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Vzhled',
            'light' => 'Světlý',
            'dark' => 'Tmavý',
            'auto' => 'Automatický',
        ],
        'locale' => 'Jazyk',
    ],

    'login_page' => [
        'username' => 'Login',
        'password' => 'Heslo',
        'sign_in' => 'Přihlásit',
        'forgot_password' => 'Zapomněli jste heslo?',
        'remember_me' => 'Pamatuj si mě',
    ],

    'exception' => [
        'entity_not_found' => 'Tato položka již není dostupná.',
        'entity_remove' => 'Tato položka nemůže být smazána, neboť na ní závisí ostatní položky.',
        'forbidden_action' => 'Na této položce nemůže být provedena požadovaná akce.',
        'insufficient_entity_permission' => 'Nemáte dostatečná oprávnění pro přístup k této položce.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Nenalezeny žádné položky',
        'no-more-results' => 'Žádné další položky',
        'loading-more-results' => 'Načítají se další položky…',
    ],
];
