<?php

return [
    'page_title' => [
        'dashboard' => 'Tauler de control',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Modificar %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Crear %entity_label_singular%',
        'exception' => 'Error|Errors',
    ],

    'datagrid' => [
        'hidden_results' => 'Alguns resultats no es poden mostrar perquè no tens prou permisos',
        'no_results' => 'No s\'han trobat resultats.',
    ],

    'paginator' => [
        'first' => 'Primera',
        'previous' => 'Anterior',
        'next' => 'Següent',
        'last' => 'Última',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> de <strong>%results%</strong>',
        'results' => '{0} Cap resultat|{1} <strong>1</strong> resultat|]1,Inf] <strong>%count%</strong> resultats',
    ],

    'label' => [
        'true' => 'Sí',
        'false' => 'No',
        'empty' => 'Buit',
        'null' => 'Nul',
        'object' => 'Objecte PHP',
        'inaccessible' => 'Inaccessible',
        'inaccessible.explanation' => 'Aquest camp no té un mètode "getter" o la propietat associada no és pública',
        'form.empty_value' => 'Cap',
    ],

    'field' => [
        'code_editor.view_code' => 'Veure codi',
        'text_editor.view_content' => 'Veure contingut',
    ],

    'action' => [
        'entity_actions' => 'Accions',
        'new' => 'Crear %entity_label_singular%',
        'search' => 'Cercar',
        'detail' => 'Veure',
        'edit' => 'Modificar',
        'delete' => 'Esborrar',
        'cancel' => 'Cancel·lar',
        'index' => 'Tornar al llistat',
        'deselect' => 'Anul·lar selecció',
        'add_new_item' => 'Afegir un element',
        'remove_item' => 'Eliminar aquest element',
        'choose_file' => 'Tria un fitxer',
        'close' => 'Tancar',
        'create' => 'Crear',
        'create_and_add_another' => 'Crear i afegir-ne un altre',
        'create_and_continue' => 'Crear i continuar editant',
        'save' => 'Desar els canvis',
        'save_and_continue' => 'Desar i continuar editant',
    ],

    'batch_action_modal' => [
        'title' => 'S\'aplicarà l\'acció %action_name% a %num_items% element(s).',
        'content' => 'Aquesta acció no es pot desfer.',
        'action' => 'Continuar',
    ],

    'delete_modal' => [
        'title' => 'Realment vols esborrar aquest element?',
        'content' => 'Aquesta acció no es pot desfer.',
    ],

    'filter' => [
        'title' => 'Filtres',
        'button.clear' => 'Netejar',
        'button.apply' => 'Aplicar',
        'label.is_equal_to' => 'és igual a',
        'label.is_not_equal_to' => 'no és igual a',
        'label.is_greater_than' => 'és més gran que',
        'label.is_greater_than_or_equal_to' => 'és més gran o igual a',
        'label.is_less_than' => 'és menor que',
        'label.is_less_than_or_equal_to' => 'és menor o igual a',
        'label.is_between' => 'està entre',
        'label.contains' => 'conté',
        'label.not_contains' => 'no conté',
        'label.starts_with' => 'comença amb',
        'label.ends_with' => 'acaba amb',
        'label.exactly' => 'exactament',
        'label.not_exactly' => 'no exactament',
        'label.is_same' => 'és el mateix',
        'label.is_not_same' => 'no és el mateix',
        'label.is_after' => 'és després',
        'label.is_after_or_same' => 'és després o el mateix',
        'label.is_before' => 'és abans',
        'label.is_before_or_same' => 'és abans o el mateix',
    ],

    'form' => [
        'are_you_sure' => 'No has desat els canvis fets en aquest formulari.',
        'tab.error_badge_title' => 'Una entrada no vàlida|%count% entrades no vàlides',
        'slug.confirm_text' => 'Si canvies l\'slug, pots trencar els enllaços d\'altres pàgines.',
    ],

    'user' => [
        'logged_in_as' => 'Connectat com a',
        'unnamed' => 'Usuari sense nom',
        'anonymous' => 'Usuari anònim',
        'sign_out' => 'Tanca la sessió',
        'exit_impersonation' => 'Sortir de la suplantació',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Aspecte',
            'light' => 'Clar',
            'dark' => 'Fosc',
            'auto' => 'Automàtic',
        ],
        'locale' => 'Llenguatge',
    ],

    'login_page' => [
        'username' => 'Nom d\'usuari',
        'password' => 'Contrasenya',
        'sign_in' => 'Iniciar sessió',
        'forgot_password' => 'Has oblidat la teva contrasenya?',
        'remember_me' => 'Recorda\'m',
    ],

    'exception' => [
        'entity_not_found' => 'Aquest element ja no està disponible.',
        'entity_remove' => 'Aquest element no es pot suprimir perquè altres elements en depenen.',
        'forbidden_action' => 'L\'acció sol·licitada no es pot dur a terme en aquest element.',
        'insufficient_entity_permission' => 'No tens permís per accedir a aquest element.',
    ],

    'autocomplete' => [
        'no-results-found' => 'No s\'han trobat resultats',
        'no-more-results' => 'No hi ha més resultats',
        'loading-more-results' => 'Carregant més resultats…',
    ],
];
