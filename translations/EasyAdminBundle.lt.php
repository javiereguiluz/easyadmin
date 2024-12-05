<?php

return [
    'page_title' => [
        'dashboard' => 'Skydelis',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Redaguoti %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Sukurti %entity_label_singular%',
        'exception' => 'Klaida|Klaidos',
    ],

    'datagrid' => [
        'hidden_results' => 'Kai kurie elementai negali būti parodyti nes jums trūkstą teisių.',
        'no_results' => 'Rezultatų nerasta.',
    ],

    'paginator' => [
        'first' => 'Pirmas',
        'previous' => 'Ankstesnis',
        'next' => 'Sekantis',
        'last' => 'Paskutinis',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> iš <strong>%results%</strong>',
        'results' => '{0} Nėra rezultatų|{1} <strong>1</strong> rezultatas|]1,Inf] <strong>%count%</strong> rezultatai',
    ],

    'label' => [
        'true' => 'Taip',
        'false' => 'Ne',
        'empty' => 'Tuščia',
        'null' => 'Null',
        'object' => 'PHP Objektas',
        'inaccessible' => 'Nepasiekiama',
        'inaccessible.explanation' => 'Getter metodas neegzistuoja šiame lauke arba nuosavybė nėra vieša',
        'form.empty_value' => 'Nenurodyta',
    ],

    'field' => [
        'code_editor.view_code' => 'Peržiūrėti kodą',
        'text_editor.view_content' => 'Peržiūrėti turinį',
    ],

    'action' => [
        'entity_actions' => 'Veiksmai',
        'new' => 'Sukurti %entity_label_singular%',
        'search' => 'Ieškoti',
        'detail' => 'Rodyti',
        'edit' => 'Redaguoti',
        'delete' => 'Pašalinti',
        'cancel' => 'Atšaukti',
        'index' => 'Grįžti į sąrašą',
        'deselect' => 'Nužymėti',
        'add_new_item' => 'Pridėti naują elementą',
        'remove_item' => 'Pašalinti elementą',
        'choose_file' => 'Pasirinkti failą',
        'close' => 'Užverti',
        'create' => 'Sukurti',
        'create_and_add_another' => 'Sukurti ir pridėti kitą',
        'create_and_continue' => 'Sukurti ir tęsti redagavimą',
        'save' => 'Išsaugoti',
        'save_and_continue' => 'Išsaugoti ir tęsti redagavimą',
    ],

    'batch_action_modal' => [
        'title' => 'Ar tikrai norite pakeisti pažymėtus elementus?',
        'content' => 'Šios operacijos atkurti nebegalėsite.',
        'action' => 'Tęsti',
    ],

    'delete_modal' => [
        'title' => 'Ar tikrai norite ištrinti šį elementą?',
        'content' => 'Šios operacijos atkurti nebegalėsite.',
    ],

    'filter' => [
        'title' => 'Filtrai',
        'button.clear' => 'Išvalyti',
        'button.apply' => 'Taikyti',
        'label.is_equal_to' => 'lygus',
        'label.is_not_equal_to' => 'nelygus',
        'label.is_greater_than' => 'didesnis',
        'label.is_greater_than_or_equal_to' => 'didesnis arba lygus',
        'label.is_less_than' => 'mažesnis',
        'label.is_less_than_or_equal_to' => 'mažesnis arba lygus',
        'label.is_between' => 'tarp',
        'label.contains' => 'turi',
        'label.not_contains' => 'neturi',
        'label.starts_with' => 'prasideda',
        'label.ends_with' => 'pasibaigia',
        'label.exactly' => 'tikslai toks',
        'label.not_exactly' => 'ne tiksliai',
        'label.is_same' => 'toks pat',
        'label.is_not_same' => 'ne toks pat',
        'label.is_after' => 'po',
        'label.is_after_or_same' => 'po arba toks pat',
        'label.is_before' => 'prieš',
        'label.is_before_or_same' => 'prieš arba toks pat',
    ],

    'form' => [
        'are_you_sure' => 'Jūs neįrašėte šios formos pakeitimų.',
        'tab.error_badge_title' => 'Viena neteisingą įvesti|%count% neteisingų įvesčių',
    ],

    'user' => [
        'logged_in_as' => 'Prisijungta kaip',
        'unnamed' => 'Neįvardintas vartotojas',
        'anonymous' => 'Anonimiškas vartotojas',
        'sign_out' => 'Atsijungti',
        'exit_impersonation' => 'Baigti apsimetimą',
    ],

    'login_page' => [
        'username' => 'Vartotojo vardas',
        'password' => 'Slaptažodis',
        'sign_in' => 'Prisijungti',
        'forgot_password' => 'Pamiršote slaptažodį?',
        'remember_me' => 'Prisiminti mane',
    ],

    'exception' => [
        'entity_not_found' => 'Šis elementas nebepasiekiamas.',
        'entity_remove' => 'Šis elementas negali būti ištrintas, nes nuo jo priklauso kiti elementai.',
        'forbidden_action' => 'Norimas atlikti veiksmas šiam elementui negalimas.',
        'insufficient_entity_permission' => 'Jums trūkstą teisių pasiekti šį elementą.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Atitikmenų nerasta',
        // 'no-more-results' => 'No more results',
        'loading-more-results' => 'Kraunama daugiau rezultatų…',
    ],
];
