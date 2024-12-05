<?php

return [
    'page_title' => [
        'dashboard' => 'Forsiden',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Rediger %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Opprett %entity_label_singular%',
        'exception' => 'Feilmelding|Feilmeldinger',
    ],

    'datagrid' => [
        'hidden_results' => 'Noen resultater kan ikke vises fordi du ikke har nok rettigheter',
        'no_results' => 'Ingen resultater.',
    ],

    'paginator' => [
        'first' => 'Første',
        'previous' => 'Forrige',
        'next' => 'Neste',
        'last' => 'Siste',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> av <strong>%results%</strong>',
        'results' => '{0} Ingen resultater|{1} <strong>1</strong> resultat|]1,Inf] <strong>%count%</strong> resultater',
    ],

    'label' => [
        'true' => 'Ja',
        'false' => 'Nei',
        'empty' => 'Tom',
        'null' => 'Null',
        'object' => 'PHP Objekt',
        'inaccessible' => 'Utilgjengelig',
        'inaccessible.explanation' => 'Getter metode finnes ikke for dette feltet eller attributten er ikke public',
        'form.empty_value' => 'Ingen',
    ],

    'field' => [
        'code_editor.view_code' => 'Vis kode',
        'text_editor.view_content' => 'Vis innhold',
    ],

    'action' => [
        'entity_actions' => 'Handlinger',
        'new' => 'Legg til %entity_label_singular%',
        'search' => 'Søk',
        'detail' => 'Vis',
        'edit' => 'Rediger',
        'delete' => 'Slett',
        'cancel' => 'Avbryt',
        'index' => 'Tilbake til liste',
        'deselect' => 'Fjern',
        'add_new_item' => 'Legg til nytt element',
        'remove_item' => 'Fjern elementet',
        'choose_file' => 'Velg fil',
        'close' => 'Lukk',
        'create' => 'Lag',
        'create_and_add_another' => 'Lagre og lag en til',
        'create_and_continue' => 'Lagre og fortsett redigering',
        // 'save' => 'Lagre endringer',
        'save_and_continue' => 'Lagre og fortsett redigering',
    ],

    'batch_action_modal' => [
        'title' => 'Du vil utføre "%action_name%" handlingen for %num_items% element(er).',
        'content' => 'Du kan ikke angre denne handlingen.',
        'action' => 'Utfør handlinger',
    ],

    'delete_modal' => [
        'title' => 'Vil du virkelig slette dette elementet?',
        'content' => 'Du kan ikke angre dette valget.',
    ],

    'filter' => [
        'title' => 'Filtre',
        'button.clear' => 'Tøm',
        'button.apply' => 'Bruk',
        'label.is_equal_to' => 'er lik',
        'label.is_not_equal_to' => 'er ikke lik',
        'label.is_greater_than' => 'er større enn',
        'label.is_greater_than_or_equal_to' => 'er større eller lik som',
        'label.is_less_than' => 'er mindre enn',
        'label.is_less_than_or_equal_to' => 'er mindre enn eller lik som',
        'label.is_between' => 'er mellom',
        'label.contains' => 'inneholder',
        'label.not_contains' => 'inneholder ikke',
        'label.starts_with' => 'starter med',
        'label.ends_with' => 'ender med',
        'label.exactly' => 'lik',
        'label.not_exactly' => 'ikke lik',
        'label.is_same' => 'er like',
        'label.is_not_same' => 'er ikke like',
        'label.is_after' => 'er etter',
        'label.is_after_or_same' => 'er etter eller samme',
        'label.is_before' => 'er før',
        'label.is_before_or_same' => 'er før eller samme',
    ],

    'form' => [
        'are_you_sure' => 'Du har ikke lagret endringene du har gjort i dette skjemaet.',
        'tab.error_badge_title' => 'En ugyldig verdi|%count% ugyldige verdier',
    ],

    'user' => [
        'logged_in_as' => 'Logget inn som',
        'unnamed' => 'Navnløs bruker',
        'anonymous' => 'Anonym bruker',
        'sign_out' => 'Logg ut',
        'exit_impersonation' => 'Slutt å etterligne',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Utseende',
            'light' => 'Lyst',
            'dark' => 'Mørkt',
            'auto' => 'Automatisk',
        ],
        'locale' => 'Språk',
    ],

    'login_page' => [
        'username' => 'Brukernavn',
        'password' => 'Passord',
        'sign_in' => 'Logg inn',
        // 'forgot_password' => '',
        // 'remember_me' => '',
    ],

    'exception' => [
        'entity_not_found' => 'Elementet er ikke lenger tilgjengelig.',
        'entity_remove' => 'Elementet kan ikke slettes fordi andre elementer er avhengig av det.',
        'forbidden_action' => 'Den angitte handlingen kan ikke utføres på dette elementet.',
        'insufficient_entity_permission' => 'Du har ikke tilgang til dette elementet.',
    ],

    'autocomplete' => [
        // 'no-results-found' => 'No results found',
        // 'no-more-results' => 'No more results',
        // 'loading-more-results' => 'Loading more results…',
    ],
];
