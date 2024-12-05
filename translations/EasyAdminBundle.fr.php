<?php

return [
    'page_title' => [
        'dashboard' => 'Tableau de bord',
        'detail' => '%entity_label_singular% <small>(#%entity_short_id%)</small>',
        'edit' => 'Modifier %entity_label_singular%',
        'index' => '%entity_label_plural%',
        'new' => 'Créer "%entity_label_singular%"',
        'exception' => 'Erreur|Erreurs',
    ],

    'datagrid' => [
        'hidden_results' => 'Certains résultats ne peuvent pas être affichés car vous n\'avez pas la permission',
        'no_results' => 'Aucun résultat trouvé',
    ],

    'paginator' => [
        'first' => 'Premier',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'last' => 'Dernier',
        'counter' => '<strong>%start%</strong> - <strong>%end%</strong> sur <strong>%results%</strong>',
        'results' => '{0} Aucun résultat|{1} <strong>1</strong> résultat|]1,Inf] <strong>%count%</strong> résultats',
    ],

    'label' => [
        'true' => 'Oui',
        'false' => 'Non',
        'empty' => 'Vide',
        'null' => 'Aucun(e)',
        'object' => 'Objet PHP',
        'inaccessible' => 'Inaccessible',
        'inaccessible.explanation' => 'Aucun accesseur n\'existe pour cette propriété ou celle-ci n\'est pas publique.',
        'form.empty_value' => 'Aucun(e)',
    ],

    'field' => [
        'code_editor.view_code' => 'Voir le code',
        'text_editor.view_content' => 'Voir le contenu',
    ],

    'action' => [
        'entity_actions' => 'Actions',
        'new' => 'Créer %entity_label_singular%',
        'search' => 'Rechercher',
        'detail' => 'Consulter',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'cancel' => 'Annuler',
        'index' => 'Retour à la liste',
        'deselect' => 'Désélectionner',
        'add_new_item' => 'Ajouter un nouvel élément',
        'remove_item' => 'Supprimer l\'élément',
        'choose_file' => 'Choisir un fichier',
        'close' => 'Fermer',
        'create' => 'Créer',
        'create_and_add_another' => 'Créer et ajouter un nouvel élément',
        'create_and_continue' => 'Créer et modifier',
        'save' => 'Sauvegarder les modifications',
        'save_and_continue' => 'Sauvegarder et modifier',
    ],

    'batch_action_modal' => [
        'title' => 'Vous allez appliquer l\'action "%action_name%" à %num_items% élément(s).',
        'content' => 'Cette action est irréversible.',
        'action' => 'Procéder',
    ],

    'delete_modal' => [
        'title' => 'Voulez-vous supprimer cet élément ?',
        'content' => 'Cette action est irréversible.',
    ],

    'filter' => [
        'title' => 'Filtres',
        'button.clear' => 'Effacer',
        'button.apply' => 'Appliquer',
        'label.is_equal_to' => 'est égal(e) à',
        'label.is_not_equal_to' => 'est différent(e) de',
        'label.is_greater_than' => 'est supérieur(e) à',
        'label.is_greater_than_or_equal_to' => 'est supérieur(e) ou égal(e) à',
        'label.is_less_than' => 'est inférieur(e) à',
        'label.is_less_than_or_equal_to' => 'est inférieur(e) ou égal(e) à',
        'label.is_between' => 'est entre',
        'label.contains' => 'contient',
        'label.not_contains' => 'ne contient pas',
        'label.starts_with' => 'commence par',
        'label.ends_with' => 'finit par',
        'label.exactly' => 'est strictement égal(e) à',
        'label.not_exactly' => 'est strictement différent(e) de',
        'label.is_same' => 'est',
        'label.is_not_same' => 'n\'est pas',
        'label.is_after' => 'est postérieure à',
        'label.is_after_or_same' => 'est postérieure à ou est le',
        'label.is_before' => 'est antérieure à',
        'label.is_before_or_same' => 'est antérieure à ou est le',
    ],

    'form' => [
        'are_you_sure' => 'Vous n\'avez pas sauvegardé vos modifications.',
        'tab.error_badge_title' => '1 champ invalide|%count% champs invalides',
        'slug.confirm_text' => 'Si vous modifiez le slug, vous pouvez casser des liens sur d\'autres pages.',
    ],

    'user' => [
        'logged_in_as' => 'Connecté en tant que',
        'unnamed' => 'Utilisateur sans nom',
        'anonymous' => 'Utilisateur anonyme',
        'sign_out' => 'Déconnexion',
        'exit_impersonation' => 'Arrêter l\'impersonnalisation',
    ],

    'settings' => [
        'appearance' => [
            'label' => 'Mode d\'apparence',
            'light' => 'Clair',
            'dark' => 'Sombre',
            'auto' => 'Automatique',
        ],
        'locale' => 'Langue',
    ],

    'login_page' => [
        'username' => 'Identifiant',
        'password' => 'Mot de passe',
        'sign_in' => 'Connectez-vous',
        'forgot_password' => 'Mot de passe oublié ?',
        'remember_me' => 'Rester connecté',
    ],

    'exception' => [
        'entity_not_found' => 'Cet élément n\'est plus disponible.',
        'entity_remove' => 'Cet élément ne peut être supprimé car d\'autres éléments en dépendent.',
        'forbidden_action' => 'L\'action demandée ne peut être exécutée sur cet élément.',
        'insufficient_entity_permission' => 'Vous n\'êtes pas autorisé à accéder à cet élément.',
    ],

    'autocomplete' => [
        'no-results-found' => 'Aucun résultat trouvé',
        'no-more-results' => 'Aucun autre résultat trouvé',
        'loading-more-results' => 'Chargement de résultats supplémentaires…',
    ],
];
