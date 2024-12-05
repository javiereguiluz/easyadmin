<?php

return [
    'page_title' => [
        'dashboard' => 'Hasiera',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular% aldatu',
        'index' => '%entity_label_plural%',
        'new' => '%entity_label_singular%-a sortu',
        'exception' => 'Errore|Erroreak',
    ],

    'datagrid' => [
        'hidden_results' => 'Emaitza batzuk ezin dira erakutsi, ez baituzu behar den adina baimen',
        'no_results' => 'Ez da emaitzarik aurkitu.',
    ],

    'paginator' => [
        'first' => 'Lehena',
        'previous' => 'Aurrekoa',
        'next' => 'Hurrengoa',
        'last' => 'Azkena',
        'counter' => '<strong>%start%</strong>tik - <strong>%end%</strong>era <strong>%results%</strong>tik',
        'results' => '{0} ez dago emaitzarik|{1} emaitza <strong>1</strong> |]1,Inf] <strong>%count%</strong> emaitza',
    ],

    'label' => [
        'true' => 'Bai',
        'false' => 'Ez',
        'empty' => 'Hutsik',
        'null' => 'Nulu',
        'object' => 'PHP Objektua',
        'inaccessible' => 'Helezina',
        'inaccessible.explanation' => 'Eremu honek ez du getter metodo bat edo honi loturiko propietatea ez da publikoa',
        'form.empty_value' => 'Bat ere ez',
    ],

    'field' => [
        'code_editor.view_code' => 'Kodea ikusi',
        'text_editor.view_content' => 'Edukia ikusi',
    ],

    'action' => [
        'entity_actions' => 'Akzioak',
        'new' => '%entity_label_singular%-a sortu',
        'search' => 'Bilatu',
        'detail' => 'Ikusi',
        'edit' => 'Aldatu',
        'delete' => 'Ezabatu',
        'cancel' => 'Ezeztatu',
        'index' => 'Zerrendara itzuli',
        'deselect' => 'Desaukeratu',
        'add_new_item' => 'Elementu bat erantsi',
        'remove_item' => 'Elementu hau ezabatu',
        'choose_file' => 'Hautatu fitxategi bat',
        'close' => 'Itxi',
        'create' => 'Sortu',
        'create_and_add_another' => 'Sortu eta beste bat gehitu',
        'create_and_continue' => 'Sortu eta jarraitu',
        'save' => 'Gorde',
        'save_and_continue' => 'Gorde eta jarraitu',
    ],

    'batch_action_modal' => [
        'title' => '%num_items% elementuri "%action_name%" ekintza aplikatu behar diozu',
        'content' => 'Ekintza hau ezin da desegin.',
        'action' => 'Aurrera',
    ],

    'delete_modal' => [
        'title' => 'Ziur zaude elementu hau ezabatu nahi duzula?',
        'content' => 'Ekintza hau ezin da desegin.',
    ],

    'filter' => [
        'title' => 'Iragazi',
        'button.clear' => 'Ezabatu',
        'button.apply' => 'Ezarri',
        'label.is_equal_to' => 'berdina da',
        'label.is_not_equal_to' => 'ez da berdina',
        'label.is_greater_than' => 'baina handiagoa da',
        'label.is_greater_than_or_equal_to' => 'handiagoa edo berdina da',
        'label.is_less_than' => 'baino txikiagoa da',
        'label.is_less_than_or_equal_to' => 'txikiagoa edo berdina da',
        'label.is_between' => 'tartean dago',
        'label.contains' => 'dauka',
        'label.not_contains' => 'ez dauka',
        'label.starts_with' => 'honela hasten da',
        'label.ends_with' => 'honela amaitzen da',
        'label.exactly' => 'zehazki',
        'label.not_exactly' => 'ez da zehazki',
        'label.is_same' => 'berdina da',
        'label.is_not_same' => 'ez da berdina',
        'label.is_after' => 'honen ondorengoa da',
        'label.is_after_or_same' => 'honen ondorengoa edo berdina da',
        'label.is_before' => 'aurrekoa da',
        'label.is_before_or_same' => 'aurrekoa edo berdina da',
    ],

    'form' => [
        'are_you_sure' => 'Formularioen aldaketak ez dituzu gorde.',
        'tab.error_badge_title' => 'Sarrera baliogabea|%count% sarrera baliogabe',
        'slug.confirm_text' => 'Esteka aldatzen baduzu, beste orri batzuetako erreferentziak hautsi ditzazkezu.',
    ],

    'user' => [
        'logged_in_as' => 'Konektatutako erabiltzailea',
        'unnamed' => 'Erabiltzaile izengabea',
        'anonymous' => 'Erabiltzaile anonimoa',
        'sign_out' => 'Amaitu saioa',
        'exit_impersonation' => 'Amaitu inpersonazioa',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Itxura',
            'light' => 'Argia',
            'dark' => 'Iluna',
            'auto' => 'Automatikoa',
        ],
        'locale' => 'Hizkuntza',
    ],

    'login_page' => [
        'username' => 'Erabiltzailea',
        'password' => 'Pasahitza',
        'sign_in' => 'Hasi saioa',
        'forgot_password' => 'Pasahitza ahaztuta?',
        'remember_me' => 'Gogoratu',
    ],

    'exception' => [
        'entity_not_found' => 'Elementu hau ez dago erabilgarri',
        'entity_remove' => 'Artikulu hau ezin da ezabatu, beste elementu batzuk haren mende daudelako.',
        'forbidden_action' => 'Elementu honetan ezin da eskatutako ekintza burutu',
        'insufficient_entity_permission' => 'Artikulu hau eskuratzeko ez duzu baimenik.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Ez da bat datorrenik aurkitu',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Emaitza gehiago kargatzen…',
    ],
];
