<?php

return [
    'page_title' => [
        'dashboard' => 'Dashboard',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => '%entity_label_singular% bearbeiten',
        'index' => '%entity_label_plural%',
        'new' => '%entity_label_singular% erstellen',
        'exception' => 'Fehler',
    ],

    'datagrid' => [
        'hidden_results' => 'Einige Ergebnisse können aufgrund fehlender Berechtigungen nicht angezeigt werden.',
        'no_results' => 'Keine Ergebnisse gefunden.',
    ],

    'paginator' => [
        'first' => 'Erste',
        'previous' => 'Zurück',
        'next' => 'Nächste',
        'last' => 'Letzte',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> von <strong>%results%</strong>',
        'results' => '{0} Keine Ergebnisse|{1} <strong>1</strong> Ergebnis|]1,Inf] <strong>%count%</strong> Ergebnisse',
    ],

    'label' => [
        'true' => 'Ja',
        'false' => 'Nein',
        'empty' => 'Leer',
        'null' => 'Null',
        'object' => 'PHP-Objekt',
        'inaccessible' => 'Nicht zugreifbar',
        'inaccessible.explanation' => 'Es gibt keine Getter-Methode für diese Eigenschaft oder die Eigenschaft ist nicht public',
        'form.empty_value' => 'kein Wert',
    ],

    'field' => [
        'code_editor.view_code' => 'Code anzeigen',
        'text_editor.view_content' => 'Inhalt anzeigen',
    ],

    'action' => [
        'entity_actions' => 'Aktionen',
        'new' => '%entity_label_singular% erstellen',
        'search' => 'Suchen',
        'detail' => 'Anzeigen',
        'edit' => 'Ändern',
        'delete' => 'Löschen',
        'cancel' => 'Abbrechen',
        'index' => 'Zurück zur Übersicht',
        'deselect' => 'Auswahl aufheben',
        'add_new_item' => 'Neues Element hinzufügen',
        'remove_item' => 'Element entfernen',
        'choose_file' => 'Datei auswählen',
        'close' => 'Schließen',
        'create' => 'Erstellen',
        'create_and_add_another' => 'Erstellen und weiteres Element hinzufügen',
        'create_and_continue' => 'Erstellen und weiter bearbeiten',
        'save' => 'Speichern',
        'save_and_continue' => 'Speichern und weiter bearbeiten',
    ],

    'batch_action_modal' => [
        'title' => 'Möchten Sie die ausgewählten Elemente wirklich verändern?',
        'content' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
        'action' => 'Fortfahren',
    ],

    'delete_modal' => [
        'title' => 'Soll das Element wirklich gelöscht werden?',
        'content' => 'Diese Aktion kann nicht rückgängig gemacht werden.',
    ],

    'filter' => [
        'title' => 'Filtern',
        'button.clear' => 'Zurücksetzen',
        'button.apply' => 'Anwenden',
        'label.is_equal_to' => 'ist gleich',
        'label.is_not_equal_to' => 'ist nicht gleich',
        'label.is_greater_than' => 'ist größer als',
        'label.is_greater_than_or_equal_to' => 'ist größer oder gleich',
        'label.is_less_than' => 'ist kleiner als',
        'label.is_less_than_or_equal_to' => 'ist kleiner oder gleich',
        'label.is_between' => 'ist zwischen',
        'label.contains' => 'enthält',
        'label.not_contains' => 'enthält nicht',
        'label.starts_with' => 'beginnt mit',
        'label.ends_with' => 'endet mit',
        'label.exactly' => 'ist genau',
        'label.not_exactly' => 'ist nicht genau',
        'label.is_same' => 'ist gleich',
        'label.is_not_same' => 'ist nicht gleich',
        'label.is_after' => 'ist nach',
        'label.is_after_or_same' => 'ist nach oder gleich',
        'label.is_before' => 'ist vor',
        'label.is_before_or_same' => 'ist vor oder gleich',
    ],

    'form' => [
        'are_you_sure' => 'Vorgenommene Änderungen wurden noch nicht gespeichert.',
        'tab.error_badge_title' => 'Eine ungültige Eingabe|%count% ungültige Eingaben',
        'slug.confirm_text' => 'Wenn Sie den Slug ändern, kann dies Links auf anderen Seiten beschädigen.',
    ],

    'user' => [
        'logged_in_as' => 'Angemeldet als',
        'unnamed' => 'Unbenannter Benutzer',
        'anonymous' => 'Anonymer Benutzer',
        'sign_out' => 'Abmelden',
        'exit_impersonation' => 'Benutzerimitation verlassen',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Erscheinungsbild',
            'light' => 'Hell',
            'dark' => 'Dunkel',
            'auto' => 'Automatisch',
        ],
        'locale' => 'Sprache',
    ],

    'login_page' => [
        'username' => 'Benutzername',
        'password' => 'Passwort',
        'sign_in' => 'Login',
        'forgot_password' => 'Passwort vergessen?',
        'remember_me' => 'Beim nächsten Mal automatisch anmelden',
    ],

    'exception' => [
        'entity_not_found' => 'Dieses Element ist nicht mehr verfügbar.',
        'entity_remove' => 'Dieses Element kann nicht gelöscht werden, weil andere Elemente davon abhängen.',
        'forbidden_action' => 'Die gewünschte Aktion kann mit diesem Element nicht ausgeführt werden.',
        'insufficient_entity_permission' => 'Sie haben keine Berechtigung, auf dieses Element zuzugreifen.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Keine Übereinstimmungen gefunden',
        'no-more-results' => 'Keine weiteren Ergebnisse',
        'loading-more-results' => 'Lade mehr Ergebnisse…',
    ],
];
