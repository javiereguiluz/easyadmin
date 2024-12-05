<?php

return [
    'page_title' => [
        'dashboard' => 'Dashboard',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Ret %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Opret %entity_label_singular%',
        'exception' => 'Fejl|Fejl',
    ],

    'datagrid' => [
        'hidden_results' => 'Nogle resultater kan ikke vises fordi du ikke har nok rettigheder',
        'no_results' => 'Intet resultat.',
    ],

    'paginator' => [
        'first' => 'Første',
        'previous' => 'Forrige',
        'next' => 'Næste',
        'last' => 'Sidste',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> af <strong>%results%</strong>',
        'results' => '{0} Ingen resultater|{1} <strong>1</strong> resultat|]1,Inf] <strong>%count%</strong> resultater',
    ],

    'label' => [
        'true' => 'Ja',
        'false' => 'Nej',
        'empty' => 'Tom',
        'null' => 'Null',
        'object' => 'PHP Objekt',
        'inaccessible' => 'Utilgængelig',
        'inaccessible.explanation' => 'Der findes ingen getter metode for dette felt eller også er det ikke et tilgængeligt felt',
        'form.empty_value' => 'Ingen',
    ],

    'field' => [
        'code_editor.view_code' => 'Vis kode',
        'text_editor.view_content' => 'Vis indhold',
    ],

    'action' => [
        'entity_actions' => 'Handlinger',
        'new' => 'Tilføj %entity_label_singular%',
        'search' => 'Søg',
        'detail' => 'Vis',
        'edit' => 'Ret',
        'delete' => 'Slet',
        'cancel' => 'Afbryd',
        'index' => 'Tilbage til listen',
        'deselect' => 'Fravælg',
        'add_new_item' => 'Tilføj nyt emne',
        'remove_item' => 'Slet emnet',
        'choose_file' => 'Vælg fil',
        'close' => 'Luk',
        'create' => 'Opret',
        'create_and_add_another' => 'Opret og tilføj ny',
        'create_and_continue' => 'Opret og fortsæt redigering',
        'save' => 'Gem ændringer',
        'save_and_continue' => 'Gem og fortsæt redigering',
    ],

    'batch_action_modal' => [
        'title' => 'Du vil anvende "%action_name%" handlingen på %num_items% element(er).',
        'content' => 'Du kan ikke fortryde denne handling.',
        'action' => 'Udfør handling',
    ],

    'delete_modal' => [
        'title' => 'Er du sikker på du vil slette dette element?',
        'content' => 'Denne operation kan ikke fortrydes.',
    ],

    'filter' => [
        'title' => 'Filtre',
        'button.clear' => 'Ryd',
        'button.apply' => 'Aktiver',
        'label.is_equal_to' => 'er lig med',
        'label.is_not_equal_to' => 'er ikke lig med',
        'label.is_greater_than' => 'er større end',
        'label.is_greater_than_or_equal_to' => 'er større end eller lig med',
        'label.is_less_than' => 'er mindre end',
        'label.is_less_than_or_equal_to' => 'er mindre end eller lig med',
        'label.is_between' => 'er i mellem',
        'label.contains' => 'indeholder',
        'label.not_contains' => 'indeholder ikke',
        'label.starts_with' => 'starter med',
        'label.ends_with' => 'slutter med',
        'label.exactly' => 'præcis',
        'label.not_exactly' => 'ikke præcis',
        'label.is_same' => 'er samme som',
        'label.is_not_same' => 'er ikke samme som',
        'label.is_after' => 'er efter',
        'label.is_after_or_same' => 'er efter eller samme som',
        'label.is_before' => 'er før',
        'label.is_before_or_same' => 'er før eller samme som',
    ],

    'form' => [
        'are_you_sure' => 'Du har ikke gemt ændringerne til denne form.',
        'tab.error_badge_title' => 'En ugyldig indtastning|%count% ugyldige indtastninger',
    ],

    'user' => [
        'logged_in_as' => 'Logget ind som',
        'unnamed' => 'Unavngiven bruger',
        'anonymous' => 'Anonym bruger',
        'sign_out' => 'Log ud',
        'exit_impersonation' => 'Stop brugerovertagelse',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Udseende',
            'light' => 'Lys',
            'dark' => 'Mørk',
            'auto' => 'Automatisk',
        ],
        'locale' => 'Sprog',
    ],

    'login_page' => [
        'username' => 'Username',
        'password' => 'Password',
        'sign_in' => 'Sign in',
        'forgot_password' => 'Glemt din adgangskode?',
        'remember_me' => 'Husk mig',
    ],

    'exception' => [
        'entity_not_found' => 'Elementet er ikke længere tilgængeligt.',
        'entity_remove' => 'Dette element kan ikke slettes, da der er andre elementer der er afhængige af det.',
        'forbidden_action' => 'Denne handling kan ikke udføres på dette element.',
        'insufficient_entity_permission' => 'Du har ikke tilladelse til at tilgå dette element.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Ingen resultater fundet',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Indlæser flere resultater…',
    ],
];
