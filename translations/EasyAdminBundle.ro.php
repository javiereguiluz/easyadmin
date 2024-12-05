<?php

return [
    'page_title' => [
        'dashboard' => 'Tablou de bord',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Editează %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Creează %entity_label_singular%',
        'exception' => 'Eroare|Erori',
    ],

    'datagrid' => [
        'hidden_results' => 'Unele rezultate sunt ascunse pentru ca nu aveți drepturile necesare.',
        'no_results' => 'Niciun rezultat.',
    ],

    'paginator' => [
        'first' => 'Prima',
        'previous' => 'Anterior',
        'next' => 'Următor',
        'last' => 'Ultima',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> din <strong>%results%</strong>',
        'results' => '{0} Niciun rezultat|{1} <strong>1</strong> rezultat|]1,Inf] <strong>%count%</strong> rezultate',
    ],

    'label' => [
        'true' => 'Da',
        'false' => 'Nu',
        'empty' => 'Gol',
        'null' => 'Nul',
        'object' => 'Obiect PHP',
        'inaccessible' => 'Inaccesibil',
        'inaccessible.explanation' => 'Metoda de tip Get nu există pentru acest câmp sau proprietatea nu e publică',
        'form.empty_value' => 'Gol',
    ],

    'field' => [
        'code_editor.view_code' => 'Vezi codul',
        'text_editor.view_content' => 'Vezi conținutul',
    ],

    'action' => [
        'entity_actions' => 'Acțiuni',
        'new' => 'Adaugă %entity_label_singular%',
        'search' => 'Caută',
        'detail' => 'Vezi',
        'edit' => 'Editează',
        'delete' => 'Șterge',
        'cancel' => 'Anulează',
        'index' => 'Înapoi la listă',
        'deselect' => 'Deselectați',
        'add_new_item' => 'Adaugă un item nou',
        'remove_item' => 'Șterge un item',
        'choose_file' => 'Alege fișierul',
        'close' => 'Închide',
        'create' => 'Crează',
        'create_and_add_another' => 'Crează și adaugă un item nou',
        'create_and_continue' => 'Crează și continuă editarea',
        'save' => 'Salvează modificarile',
        'save_and_continue' => 'Salvează și continuă editarea',
    ],

    'batch_action_modal' => [
        'title' => 'Veți aplica actiunea "%action_name%" pentru %num_items% item(e).',
        'content' => 'Această acțiune este ireversibilă.',
        'action' => 'Procedează',
    ],

    'delete_modal' => [
        'title' => 'Ești sigur că vrei să ștergi acest item?',
        'content' => 'Nu există posibilitatea de a reveni asupra acestei decizii.',
    ],

    'filter' => [
        'title' => 'Filtre',
        'button.clear' => 'Ștergeți',
        'button.apply' => 'Aplică',
        'label.is_equal_to' => 'este egal cu',
        'label.is_not_equal_to' => 'este diferit de',
        'label.is_greater_than' => 'este mai mare decât',
        'label.is_greater_than_or_equal_to' => 'este mai mare sau egal cu',
        'label.is_less_than' => 'este mai mic decât',
        'label.is_less_than_or_equal_to' => 'este mai mic sau egal cu',
        'label.is_between' => 'e între',
        'label.contains' => 'conține',
        'label.not_contains' => 'nu conține',
        'label.starts_with' => 'începe cu',
        'label.ends_with' => 'termină cu',
        'label.exactly' => 'este strict egal cu',
        'label.not_exactly' => 'este strict diferit cu',
        'label.is_same' => 'este',
        'label.is_not_same' => 'nu este',
        'label.is_after' => 'este mai târziu de',
        'label.is_after_or_same' => 'este mai târziu sau este',
        'label.is_before' => 'este mai vechi de',
        'label.is_before_or_same' => 'este mai devreme sau este',
    ],

    'form' => [
        'are_you_sure' => 'Nu ați salvat modificările.',
        'tab.error_badge_title' => '1 câmp nevalid |% count% câmpuri nevalide',
        'slug.confirm_text' => 'Dacă modificați slug-ul, veți strica link-urile pe alte pagini.',
    ],

    'user' => [
        'logged_in_as' => 'Înregistrat ca',
        'unnamed' => 'Utilizator fară nume',
        'anonymous' => 'Utilizator anonim',
        'sign_out' => 'Deconectați-vă',
        'exit_impersonation' => 'Oprește impersonalizarea',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Aspect',
            'light' => 'Luminos',
            'dark' => 'Întunecat',
            'auto' => 'Automat',
        ],
        'locale' => 'Limba',
    ],

    'login_page' => [
        'username' => 'Utilizator',
        'password' => 'Parolă',
        'sign_in' => 'Autentifică-te',
        'forgot_password' => 'V-ați uitat parola?',
        'remember_me' => 'Tine-ma minte',
    ],

    'exception' => [
        'entity_not_found' => 'Acest item nu mai este disponibil.',
        'entity_remove' => 'Acest item nu poate fi șters deoarece alte iteme depind de acesta.',
        'forbidden_action' => 'Acțiunea solicitată nu poate fi efectuată asupra acestui item.',
        'insufficient_entity_permission' => 'Nu sunteți autorizat să accesați acest item.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Nu au fost găsite rezultate',
        'no-more-results' => 'Nu mai sunt rezultate',
        'loading-more-results' => 'Se încarcă mai multe rezultate…',
    ],
];
